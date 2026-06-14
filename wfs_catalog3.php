<?php
/**
 * Catalog pass 3 — grow the catalog to 100 products.
 * Adds 80 new products (20 per category) with full details:
 * descriptions, SEO meta, color/size/brand attributes, prices,
 * specials, stock, new/featured flags, and images assigned from
 * the hand-verified pool at /tmp/wfspool/{shirts,tees,kurtas,fabrics}.
 * Also re-images wfs-shirt-flannel with a true flannel photo.
 * Run:  php wfs_catalog3.php
 */

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Webkul\Product\Repositories\ProductRepository;
use Webkul\Product\Models\ProductImage;

function out($m){ echo $m.PHP_EOL; }

$productRepo = app(ProductRepository::class);
$channel     = core()->getCurrentChannel();
$localeCode  = $channel->default_locale->code ?? 'en';
app()->setLocale($localeCode);

$catIds = [];
foreach (['shirts', 't-shirts', 'kurtas', 'fabrics'] as $slug) {
    $catIds[$slug] = DB::table('category_translations')->where('slug', $slug)->value('category_id');
}

/* color option ids: 1 Red, 2 Green, 3 Yellow, 4 Black, 5 White, 10 Indigo, 11 Natural */
function colorFor(string $name): int {
    $n = strtolower($name);
    foreach ([
        'indigo' => 10, 'denim' => 10, 'chambray' => 10, 'shibori' => 10,
        'white' => 5, 'chikan' => 5, 'poplin' => 5,
        'natural' => 11, 'ecru' => 11, 'kora' => 11, 'mulmul' => 11, 'khadi' => 11, 'linen' => 11, 'greige' => 11, 'unbleached' => 11, 'hemp' => 11,
        'black' => 4, 'sateen' => 4,
        'madder' => 1, 'red' => 1,
        'olive' => 2, 'green' => 2,
        'turmeric' => 3, 'mustard' => 3, 'yellow' => 3,
    ] as $kw => $id) {
        if (str_contains($n, $kw)) return $id;
    }
    return [1, 2, 4, 5, 10, 11][crc32($name) % 6];
}

/* [name, category, price, special|null, descriptive line] */
$styles = [
    'shirts' => [
        ['Indigo Chambray Work Shirt',   1799, 1499, 'Yarn-dyed chambray with a soft, lived-in hand.'],
        ['White Poplin Dress Shirt',     1899, null, 'Fine-count poplin with a crisp, clean face.'],
        ['Natural Twill Weekend Shirt',  1699, null, 'Brushed handloom twill in an undyed ecru.'],
        ['Madras Check Summer Shirt',    1599, 1299, 'Lightweight madras checks, woven yarn-dyed.'],
        ['Herringbone Weave Shirt',      1849, null, 'Subtle herringbone texture from the loom.'],
        ['Mangalgiri Stripe Shirt',      1749, null, 'Classic Mangalgiri stripes with a missing-thread border.'],
        ['Slub Cotton Camp Shirt',       1549, 1299, 'Open camp collar in breezy slub cotton.'],
        ['Kala Cotton Field Shirt',      1999, null, 'Rain-fed indigenous Kala cotton, dense and sturdy.'],
        ['Double-Cloth Winter Shirt',    2299, 1899, 'Two layers woven as one for quiet warmth.'],
        ['Madder Stripe Shirt',          1699, null, 'Madder-root stripes on an unbleached ground.'],
        ['Olive Garment-Dyed Shirt',     1649, null, 'Dyed after sewing for tonal, uneven depth.'],
        ['Black Sateen Evening Shirt',   1949, null, 'Smooth sateen weave with a soft sheen.'],
        ['Honeycomb Weave Shirt',        1799, 1549, 'Airy honeycomb structure straight off the pit loom.'],
        ['Cutaway Collar City Shirt',    1899, null, 'Sharp cutaway collar on handloom poplin.'],
        ['Grandad Collar Shirt',         1599, null, 'Round collarless neckline in two-ply cotton.'],
        ['Selvedge Denim Western Shirt', 2199, 1849, 'Snap-front western cut in selvedge denim.'],
        ['Ecru Dobby Texture Shirt',     1749, null, 'Tiny dobby motifs woven, never printed.'],
        ['Turmeric Check Shirt',         1649, null, 'Turmeric-dyed windowpane checks.'],
        ['Two-Pocket Safari Shirt',      1849, null, 'Utility pockets, drill weave, built for wear.'],
        ['Handloom Seersucker Shirt',    1699, 1449, 'Puckered seersucker that never needs ironing.'],
    ],
    't-shirts' => [
        ['Supima Crew Tee',             799,  null, 'Long-staple Supima knit for a smooth face.'],
        ['Slub V-Neck Tee',             749,  599,  'Textured slub yarn with an easy drape.'],
        ['Indigo-Dyed Ringer Tee',      949,  null, 'Rope-dyed indigo body with contrast rib.'],
        ['Boxy Cropped Tee',            849,  null, 'Cropped, boxy cut in heavyweight jersey.'],
        ['Raglan Baseball Tee',         899,  749,  'Three-quarter raglan sleeves, vintage body.'],
        ['Ribbed Cotton Tank',          649,  null, 'Fine 2x2 rib in combed cotton.'],
        ['Oversized Drop-Shoulder Tee', 899,  null, 'Dropped shoulders and a roomy, modern fall.'],
        ['Madder-Dyed Tee',             999,  849,  'Madder-root dye, no two batches alike.'],
        ['Turmeric-Dyed Tee',           999,  null, 'Warm turmeric tone, softened with washes.'],
        ['Kora Unbleached Tee',         749,  null, 'Loom-state kora cotton, completely undyed.'],
        ['Heavyweight Loopwheel Tee',   1299, null, 'Dense loopwheeled jersey, built for years.'],
        ['Pocket Slub Tee',             799,  649,  'Single chest pocket on slub jersey.'],
        ['Marled Knit Tee',             849,  null, 'Two-tone marled yarns, knitted not printed.'],
        ['Block-Print Motif Tee',       899,  null, 'Hand block-printed chest motif.'],
        ['Vintage Wash Black Tee',      849,  699,  'Sun-faded black with raw-edge ribs.'],
        ['Breton Stripe Tee',           899,  null, 'Even Breton stripes, yarn-dyed.'],
        ['Mock Neck Tee',               949,  null, 'Raised mock neckline in dense jersey.'],
        ['Hemp-Cotton Blend Tee',       1099, 949,  'Hemp blend that breathes and lasts.'],
        ['Pique Knit Polo Tee',         1199, null, 'Breathable piqué with a knitted collar.'],
        ['Garment-Dyed Olive Tee',      849,  null, 'Olive over-dye with tonal stitching.'],
    ],
    'kurtas' => [
        ['Ajrakh Print Kurta',          1899, 1599, 'Resist-printed Ajrakh in natural dyes.'],
        ['Mangalgiri Cotton Kurta',     1499, null, 'Fine Mangalgiri cotton with woven borders.'],
        ['Jamdani Weave Kurta',         2399, null, 'Discontinuous-weft Jamdani motifs, woven by hand.'],
        ['Ikat Handloom Kurta',         1999, 1699, 'Tie-dyed yarns woven into soft ikat blurs.'],
        ['Pure Linen Kurta',            1899, null, 'European flax, Indian loom, summer ease.'],
        ['Silk-Cotton Festive Kurta',   2599, null, 'Silk-cotton blend with a quiet lustre.'],
        ['Angrakha Wrap Kurta',         2199, 1899, 'Overlap front tied with cloth buttons.'],
        ['Pathani Kurta Set',           2799, null, 'Kurta and salwar set in twill weave.'],
        ['Bandhgala Kurta',             2499, null, 'Stand collar, concealed placket, festive drape.'],
        ['Asymmetric Hem Kurta',        1799, null, 'Stepped hem with deep side slits.'],
        ['Indigo Dabu Print Kurta',     1999, 1749, 'Mud-resist dabu blocks dipped in indigo.'],
        ['Kantha Stitch Kurta',         2299, null, 'Running kantha hand-stitch across the yoke.'],
        ['Pintuck Front Kurta',         1699, null, 'Fine pintucks pressed down the placket.'],
        ['Mandarin Collar Long Kurta',  1799, 1499, 'Ankle-grazing length, mandarin collar.'],
        ['Bagru Print Kurta',           1899, null, 'Bagru black-and-madder blocks on cream.'],
        ['Tussar Silk Kurta',           2699, null, 'Wild tussar slubs with natural gold tone.'],
        ['Side-Slit Travel Kurta',      1599, 1349, 'Wrinkle-friendly weave for the road.'],
        ['Sanganeri Print Kurta',       1849, null, 'Delicate Sanganeri florals, hand-printed.'],
        ['Chanderi Occasion Kurta',     2499, 2199, 'Sheer Chanderi with zari selvedge.'],
        ['Everyday Mulmul Kurta',       1399, null, 'Feather-light mulmul for daily wear.'],
    ],
    'fabrics' => [
        ['Mulmul Cotton Fabric',        420,  null, 'Feather-weight mulmul, 44-inch width.'],
        ['Jamdani Motif Fabric',        1180, 980,  'Hand-inlaid Jamdani buti on fine cotton.'],
        ['Ikat Weave Fabric',           880,  null, 'Yarn-resist ikat in double shades.'],
        ['Ajrakh Block-Print Fabric',   960,  null, 'Sixteen-stage Ajrakh printing, natural dyes.'],
        ['Kalamkari Print Fabric',      890,  740,  'Pen-drawn Kalamkari narratives on cotton.'],
        ['Chanderi Sheer Fabric',       1080, null, 'Glass-sheer Chanderi with cotton body.'],
        ['Maheshwari Border Fabric',    990,  null, 'Reversible Maheshwari border, silk-cotton.'],
        ['Khadi Denim Fabric',          760,  640,  'Hand-spun, hand-woven khadi denim.'],
        ['Slub Linen Fabric',           840,  null, 'Heavy slub linen for jackets and totes.'],
        ['Cotton Gauze Fabric',         480,  null, 'Open gauze weave, perfect for summer.'],
        ['Madder-Dyed Fabric',          720,  610,  'Madder-root reds, dyed in small vats.'],
        ['Turmeric-Dyed Fabric',        700,  null, 'Sun-bright turmeric yellows, plant-dyed.'],
        ['Indigo Shibori Fabric',       980,  null, 'Stitch-resist shibori dipped in indigo.'],
        ['Honeycomb Towelling Fabric',  560,  470,  'Absorbent honeycomb weave for home sewing.'],
        ['Kora Greige Fabric',          390,  null, 'Loom-state greige, ready for your own dye.'],
        ['Silk Organza Fabric',         1240, null, 'Crisp silk organza with a fine sheen.'],
        ['Matka Silk Fabric',           1190, 990,  'Nubby matka silk, rustic and rich.'],
        ['Herringbone Wool Fabric',     1280, null, 'Hill-wool herringbone for overshirts.'],
        ['Bhujodi Weave Fabric',        1150, null, 'Kutchi Bhujodi weave with tribal motifs.'],
        ['Dobby Stripe Fabric',         620,  530,  'Textured dobby stripes on soft cotton.'],
    ],
];

$skuCat = ['shirts' => 'shirt', 't-shirts' => 'tee', 'kurtas' => 'kurta', 'fabrics' => 'fab'];
$poolDir = ['shirts' => 'shirts', 't-shirts' => 'tees', 'kurtas' => 'kurtas', 'fabrics' => 'fabrics'];

$done = 0;
foreach ($styles as $cat => $items) {
    $pool = glob('/tmp/wfspool/'.$poolDir[$cat].'/*.jpg');
    sort($pool);
    // deterministic shuffle so adjacent products rarely share a base image
    usort($pool, fn ($a, $b) => crc32($a) <=> crc32($b));
    $poolCount = count($pool);
    if (! $poolCount) { out("NO POOL for $cat"); continue; }

    foreach ($items as $i => [$name, $price, $special, $line]) {
        $sku = 'wfs-'.$skuCat[$cat].'-'.Str::slug(Str::limit($name, 28, ''));

        try {
            $existsId = DB::table('products')->where('sku', $sku)->value('id');

            $product = $existsId
                ? \Webkul\Product\Models\Product::find($existsId)
                : $productRepo->create(['type' => 'simple', 'attribute_family_id' => 1, 'sku' => $sku]);

            $isFabric = $cat === 'fabrics';
            $unit     = $isFabric ? ' Sold per metre, cut to order.' : '';
            $care     = $isFabric
                ? 'Cold hand-wash before first use; natural dyes settle after the first rinse.'
                : 'Cold gentle wash, dry in shade, warm iron. Natural dyes mellow beautifully with age.';

            $data = [
                'channel'              => $channel->code,
                'locale'               => $localeCode,
                'sku'                  => $sku,
                'name'                 => $name,
                'url_key'              => Str::slug($name),
                'short_description'    => '<p>'.$line.$unit.'</p>',
                'description'          => '<p>'.$line.$unit.'</p><p>Made by Weavers Fab Studio with artisan partners: woven on wooden handlooms in small batches, coloured with plant dyes (indigo, madder, turmeric) where dyed at all, and finished by hand.</p><p><strong>Care:</strong> '.$care.'</p>',
                'meta_title'           => $name.' | Weavers Fab Studio',
                'meta_keywords'        => strtolower(str_replace(' ', ', ', $name)).', handloom, natural dye',
                'meta_description'     => Str::limit($line.' Handwoven in small batches by Weavers Fab Studio.', 155),
                'price'                => $price,
                'special_price'        => $special,
                'special_price_from'   => null,
                'special_price_to'     => null,
                'weight'               => $isFabric ? 0.25 : 0.35,
                'status'               => 1,
                'new'                  => $i % 5 < 2 ? 1 : 0,
                'featured'             => $i % 4 === 0 ? 1 : 0,
                'visible_individually' => 1,
                'guest_checkout'       => 1,
                'product_number'       => strtoupper($sku),
                'manage_stock'         => 1,
                'brand'                => 12,
                'color'                => colorFor($name),
                'categories'           => [$catIds[$cat]],
                'channels'             => [$channel->id],
                'inventories'          => [1 => 25 + (crc32($sku) % 90)],
            ];

            if (! $isFabric) {
                $data['size'] = [6, 7, 8, 9][crc32($name) % 4];
            }

            $productRepo->update($data, $product->id);

            // image from the verified pool (round-robin)
            if (! ProductImage::where('product_id', $product->id)->exists()) {
                $img      = $pool[$i % $poolCount];
                $diskPath = "product/{$product->id}/{$sku}.jpg";
                Storage::disk('public')->put($diskPath, file_get_contents($img));
                ProductImage::create(['product_id' => $product->id, 'path' => $diskPath, 'position' => 1]);
            }

            $done++;
            out(($existsId ? 'updated' : 'created')." $sku (#{$product->id})");
        } catch (\Throwable $e) {
            out("ERROR $sku: ".$e->getMessage().' @ '.basename($e->getFile()).':'.$e->getLine());
        }
    }
}

/* retrofit: true flannel photo for the original flannel shirt */
try {
    $flannelId = DB::table('products')->where('sku', 'wfs-shirt-flannel')->value('id');
    if ($flannelId && is_file('/tmp/wfspool/shirts/flannel.jpg')) {
        foreach (ProductImage::where('product_id', $flannelId)->get() as $old) {
            if ($old->path && Storage::disk('public')->exists($old->path)) {
                Storage::disk('public')->delete($old->path);
            }
            $old->delete();
        }
        $diskPath = "product/{$flannelId}/wfs-shirt-flannel.jpg";
        Storage::disk('public')->put($diskPath, file_get_contents('/tmp/wfspool/shirts/flannel.jpg'));
        ProductImage::create(['product_id' => $flannelId, 'path' => $diskPath, 'position' => 1]);
        out("re-imaged wfs-shirt-flannel (#$flannelId) with true flannel");
    }
} catch (\Throwable $e) {
    out('FLANNEL ERROR: '.$e->getMessage());
}

out("---- $done products processed. reindexing ----");
try {
    \Illuminate\Support\Facades\Artisan::call('indexer:index', ['--type' => null]);
    out(trim(\Illuminate\Support\Facades\Artisan::output()));
} catch (\Throwable $e) {
    out('INDEXER ERROR: '.$e->getMessage());
}

out('total products: '.DB::table('products')->count());
out('DONE.');
