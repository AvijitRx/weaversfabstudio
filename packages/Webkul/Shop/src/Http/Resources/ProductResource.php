<?php

namespace Webkul\Shop\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Webkul\Product\Helpers\Review;

class ProductResource extends JsonResource
{
    /**
     * Create a new resource instance.
     *
     * @param  mixed  $resource
     * @return void
     */
    public function __construct($resource)
    {
        $this->reviewHelper = app(Review::class);

        parent::__construct($resource);
    }

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        $productTypeInstance = $this->getTypeInstance();

        return [
            'id' => $this->id,
            'sku' => $this->sku,
            'name' => $this->name,
            'description' => $this->description,
            'url_key' => $this->url_key,
            'base_image' => product_image()->getProductBaseImage($this),
            'images' => product_image()->getGalleryImages($this),
            'is_new' => (bool) $this->new,
            'is_featured' => (bool) $this->featured,
            'on_sale' => (bool) $productTypeInstance->haveDiscount(),
            'is_saleable' => (bool) $productTypeInstance->isSaleable(),
            'is_wishlist' => (bool) auth()->guard()->user()?->wishlist_items
                ->where('channel_id', core()->getCurrentChannel()->id)
                ->where('product_id', $this->id)->count(),
            'min_price' => core()->formatPrice($productTypeInstance->getMinimalPrice()),
            'discount_percent' => $this->getDiscountPercent($productTypeInstance),
            'prices' => $productTypeInstance->getProductPrices(),
            'price_html' => $productTypeInstance->getPriceHtml(),
            'ratings' => [
                'average' => $this->reviewHelper->getAverageRating($this),
                'total' => $this->reviewHelper->getTotalRating($this),
            ],
            'reviews' => [
                'total' => $this->reviewHelper->getTotalReviews($this),
            ],
        ];
    }

    /**
     * Discount percentage off the original price, or null when not discounted.
     *
     * Read off the price index rather than getProductPrices(), because a
     * configurable only ever returns a 'regular' key (set to the minimum final
     * price), so its discount is not derivable from that array. Never throws —
     * a product card must not be able to break a listing page.
     *
     * @param  \Webkul\Product\Type\AbstractType  $productTypeInstance
     * @return int|null
     */
    protected function getDiscountPercent($productTypeInstance)
    {
        try {
            $priceIndex = $productTypeInstance->getPriceIndex();

            if (! $priceIndex) {
                return null;
            }

            $regular = (float) $priceIndex->regular_min_price;
            $final = (float) $priceIndex->min_price;

            if (
                $regular <= 0
                || $final >= $regular
            ) {
                return null;
            }

            return (int) round((($regular - $final) / $regular) * 100);
        } catch (\Throwable $e) {
            return null;
        }
    }
}
