<?php
/**
 * Catalog pass 2 for Weavers Fab Studio.
 * - Re-images the 12 original products with curated, hand-verified photos
 *   (the first seeder pulled random keyword images).
 * - Adds 8 new products (shirts, tees, kurtas, fabrics).
 * - Sets color / size / brand attribute values so storefront filters work.
 * Images are read from /tmp/wfsfinal/{sku}.jpg (pre-downloaded + reviewed).
 * Run:  php wfs_catalog2.php
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
out('categories: '.json_encode($catIds));

/*
 * Attribute option ids (see attribute_options):
 * color: 1 Red, 2 Green, 3 Yellow, 4 Black, 5 White, 10 Indigo, 11 Natural
 * size:  6 S, 7 M, 8 L, 9 XL
 * brand: 12 Weavers Fab Studio
 */
$catalog = [
    ['sku'=>'wfs-shirt-oxford', 'name'=>'Oxford Cotton Shirt',     'price'=>1499, 'special'=>null, 'cat'=>'shirts',   'new'=>1,'feat'=>1,'color'=>10,'size'=>8, 'sd'=>'Crisp button-down in pure Oxford cotton.'],
    ['sku'=>'wfs-shirt-linen',  'name'=>'Linen Summer Shirt',      'price'=>1899, 'special'=>1499, 'cat'=>'shirts',   'new'=>1,'feat'=>0,'color'=>5, 'size'=>7, 'sd'=>'Breathable pure-linen shirt for warm days.'],
    ['sku'=>'wfs-shirt-flannel','name'=>'Checked Flannel Shirt',   'price'=>1699, 'special'=>null, 'cat'=>'shirts',   'new'=>0,'feat'=>1,'color'=>10,'size'=>8, 'sd'=>'Brushed flannel in a heritage check.'],
    ['sku'=>'wfs-shirt-denim',  'name'=>'Indigo Denim Shirt',      'price'=>1999, 'special'=>1699, 'cat'=>'shirts',   'new'=>1,'feat'=>1,'color'=>4, 'size'=>8, 'sd'=>'Dark-wash denim shirt, softened with every wear.'],
    ['sku'=>'wfs-shirt-band',   'name'=>'Band Collar Shirt',       'price'=>1599, 'special'=>null, 'cat'=>'shirts',   'new'=>1,'feat'=>0,'color'=>10,'size'=>7, 'sd'=>'Collarless shirt in soft handloom cotton.'],
    ['sku'=>'wfs-tee-crew',     'name'=>'Classic Crew Tee',        'price'=>699,  'special'=>null, 'cat'=>'t-shirts', 'new'=>1,'feat'=>1,'color'=>5, 'size'=>7, 'sd'=>'The everyday crew-neck in combed cotton.'],
    ['sku'=>'wfs-tee-pima',     'name'=>'Pima Cotton Tee',         'price'=>899,  'special'=>699,  'cat'=>'t-shirts', 'new'=>0,'feat'=>0,'color'=>4, 'size'=>8, 'sd'=>'Buttery-soft Pima cotton, built to last.'],
    ['sku'=>'wfs-tee-henley',   'name'=>'Graphic Studio Tee',      'price'=>999,  'special'=>null, 'cat'=>'t-shirts', 'new'=>1,'feat'=>0,'color'=>4, 'size'=>7, 'sd'=>'Hand-printed graphic on heavyweight cotton.'],
    ['sku'=>'wfs-tee-pocket',   'name'=>'Printed Pocket Tee',      'price'=>849,  'special'=>null, 'cat'=>'t-shirts', 'new'=>1,'feat'=>1,'color'=>11,'size'=>7, 'sd'=>'Garment-dyed tee with a block-printed chest motif.'],
    ['sku'=>'wfs-tee-long',     'name'=>'Long-Sleeve Heavy Tee',   'price'=>1199, 'special'=>949,  'cat'=>'t-shirts', 'new'=>1,'feat'=>0,'color'=>5, 'size'=>8, 'sd'=>'Heavyweight long-sleeve in unbleached cotton.'],
    ['sku'=>'wfs-kurta-cotton', 'name'=>'Handloom Cotton Kurta',   'price'=>1299, 'special'=>null, 'cat'=>'kurtas',   'new'=>1,'feat'=>1,'color'=>2, 'size'=>7, 'sd'=>'Straight-cut kurta in naturally dyed handloom cotton.'],
    ['sku'=>'wfs-kurta-silk',   'name'=>'Festive Silk Kurta',      'price'=>2499, 'special'=>1999, 'cat'=>'kurtas',   'new'=>0,'feat'=>1,'color'=>10,'size'=>8, 'sd'=>'Lustrous silk kurta for celebrations.'],
    ['sku'=>'wfs-kurta-khadi',  'name'=>'Khadi Nehru Kurta',       'price'=>1599, 'special'=>null, 'cat'=>'kurtas',   'new'=>1,'feat'=>0,'color'=>5, 'size'=>8, 'sd'=>'Hand-spun khadi with a Nehru collar.'],
    ['sku'=>'wfs-kurta-chikan', 'name'=>'Chikankari Kurta',        'price'=>2199, 'special'=>null, 'cat'=>'kurtas',   'new'=>1,'feat'=>1,'color'=>5, 'size'=>7, 'sd'=>'Hand-embroidered chikankari on fine white cotton.'],
    ['sku'=>'wfs-kurta-short',  'name'=>'Short Festive Kurta',     'price'=>1799, 'special'=>1499, 'cat'=>'kurtas',   'new'=>1,'feat'=>0,'color'=>2, 'size'=>7, 'sd'=>'Hip-length kurta in festive handloom weaves.'],
    ['sku'=>'wfs-fab-cotton',   'name'=>'Handloom Cotton Fabric',  'price'=>540,  'special'=>null, 'cat'=>'fabrics',  'new'=>0,'feat'=>1,'color'=>11,'size'=>null,'sd'=>'Naturally dyed cotton, sold per metre.'],
    ['sku'=>'wfs-fab-indigo',   'name'=>'Indigo Denim Fabric',     'price'=>720,  'special'=>590,  'cat'=>'fabrics',  'new'=>1,'feat'=>1,'color'=>10,'size'=>null,'sd'=>'Vat-dyed indigo denim by the metre.'],
    ['sku'=>'wfs-fab-silk',     'name'=>'Tussar Silk Fabric',      'price'=>980,  'special'=>null, 'cat'=>'fabrics',  'new'=>1,'feat'=>0,'color'=>1, 'size'=>null,'sd'=>'Wild tussar silk with a natural slub.'],
    ['sku'=>'wfs-fab-block',    'name'=>'Hand-Dyed Cotton Fabric', 'price'=>640,  'special'=>null, 'cat'=>'fabrics',  'new'=>1,'feat'=>0,'color'=>3, 'size'=>null,'sd'=>'Small-batch hand-dyed cotton, one-of-a-kind bolts.'],
    ['sku'=>'wfs-fab-wool',     'name'=>'Handwoven Wool Fabric',   'price'=>1150, 'special'=>null, 'cat'=>'fabrics',  'new'=>1,'feat'=>1,'color'=>11,'size'=>null,'sd'=>'Hill-loom wool, carded and spun by hand.'],
    ['sku'=>'wfs-tee-pocket-2', 'name'=>'Striped Henley Tee',      'price'=>999,  'special'=>null, 'cat'=>'t-shirts', 'new'=>0,'feat'=>0,'color'=>5, 'size'=>7, 'sd'=>'A three-button henley with fine stripes.', 'skip'=>true],
];

$done = 0;
foreach ($catalog as $p) {
    if (! empty($p['skip'])) continue;

    $imgSrc = "/tmp/wfsfinal/{$p['sku']}.jpg";
    if (! is_file($imgSrc)) { out("NO IMAGE for {$p['sku']} — skipping"); continue; }

    try {
        $existsId = DB::table('products')->where('sku', $p['sku'])->value('id');

        if ($existsId) {
            $product = \Webkul\Product\Models\Product::find($existsId);
        } else {
            $product = $productRepo->create([
                'type'                => 'simple',
                'attribute_family_id' => 1,
                'sku'                 => $p['sku'],
            ]);
        }

        $data = [
            'channel'              => $channel->code,
            'locale'               => $localeCode,
            'sku'                  => $p['sku'],
            'name'                 => $p['name'],
            'url_key'              => Str::slug($p['name']),
            'short_description'    => '<p>'.$p['sd'].'</p>',
            'description'          => '<p>'.$p['sd'].' Crafted by Weavers Fab Studio from naturally sourced fibres, finished and inspected by hand.</p>',
            'price'                => $p['price'],
            'special_price'        => $p['special'],
            'special_price_from'   => null,
            'special_price_to'     => null,
            'weight'               => 0.3,
            'status'               => 1,
            'new'                  => $p['new'],
            'featured'             => $p['feat'],
            'visible_individually' => 1,
            'guest_checkout'       => 1,
            'product_number'       => strtoupper($p['sku']),
            'manage_stock'         => 1,
            'brand'                => 12,
            'categories'           => [$catIds[$p['cat']]],
            'channels'             => [$channel->id],
            'inventories'          => [1 => rand(40, 120)],
        ];

        if ($p['color']) {
            $data['color'] = $p['color'];
        }

        if ($p['size']) {
            $data['size'] = $p['size'];
        }

        $productRepo->update($data, $product->id);

        // Replace images with the curated one.
        foreach (ProductImage::where('product_id', $product->id)->get() as $old) {
            if ($old->path && Storage::disk('public')->exists($old->path)) {
                Storage::disk('public')->delete($old->path);
            }
            $old->delete();
        }

        $diskPath = "product/{$product->id}/{$p['sku']}.jpg";
        Storage::disk('public')->put($diskPath, file_get_contents($imgSrc));

        ProductImage::create([
            'product_id' => $product->id,
            'path'       => $diskPath,
            'position'   => 1,
        ]);

        $done++;
        out(($existsId ? 'updated' : 'created')." {$p['sku']} (#{$product->id})");
    } catch (\Throwable $e) {
        out("ERROR {$p['sku']}: ".$e->getMessage().' @ '.basename($e->getFile()).':'.$e->getLine());
    }
}

out("---- $done products done. reindexing ----");
try {
    \Illuminate\Support\Facades\Artisan::call('indexer:index', ['--type' => null]);
    out(trim(\Illuminate\Support\Facades\Artisan::output()));
} catch (\Throwable $e) {
    out('INDEXER ERROR: '.$e->getMessage());
}
out('DONE.');
