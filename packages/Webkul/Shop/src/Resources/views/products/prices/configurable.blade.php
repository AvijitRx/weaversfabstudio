{{-- price-label kept (hidden) so the configurable JS that toggles it doesn't break --}}
<p class="price-label" hidden></p>

@php
    // A configurable with no (or broken) variants can come back without these
    // keys — never let a missing key raise a fatal ErrorException here.
    $regularPrice = $prices['regular'] ?? null;
    $finalPrice   = $prices['final'] ?? null;

    $isOnSale = isset($finalPrice['price'], $regularPrice['price'])
        && $finalPrice['price'] < $regularPrice['price'];

    /*
     * Configurable::getProductPrices() only ever returns a 'regular' key, and
     * sets it to the *minimum final* price across the variants — so a discounted
     * configurable would show just "₹599" with nothing struck through. The
     * original price has to come off the price index (regular_min_price).
     * Wrapped defensively: this view must never be able to throw.
     */
    $indexStrikePrice = null;

    if (! $isOnSale && isset($product)) {
        try {
            $priceIndex = $product->getTypeInstance()->getPriceIndex();

            if (
                $priceIndex
                && $priceIndex->regular_min_price > $priceIndex->min_price
            ) {
                $indexStrikePrice = core()->currency($priceIndex->regular_min_price);
            }
        } catch (\Throwable $e) {
            $indexStrikePrice = null;
        }
    }

    if ($isOnSale) {
        $strikePrice = $regularPrice['formatted_price'] ?? '';
        $showPrice   = $finalPrice['formatted_price'] ?? '';
    } else {
        $strikePrice = $indexStrikePrice ?? '';
        $showPrice   = $regularPrice['formatted_price'] ?? ($finalPrice['formatted_price'] ?? '');
    }
@endphp

{{-- both nodes always render (empty when unused) because the configurable JS toggles them --}}
<p class="regular-price text-lg font-semibold text-gray-500 line-through">
    {{ $strikePrice }}
</p>

<p class="final-price font-semibold">
    {{ $showPrice }}
</p>
