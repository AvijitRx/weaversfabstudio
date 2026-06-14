<?php
/**
 * One-off catalog seeder for Weavers Fab Studio.
 * Creates apparel categories + products the same way the admin does
 * (via CategoryRepository / ProductRepository), with real images.
 * Run:  php wfs_seed.php
 */

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Webkul\Category\Repositories\CategoryRepository;
use Webkul\Product\Repositories\ProductRepository;
use Webkul\Product\Models\ProductImage;

function out($m){ echo $m.PHP_EOL; }

$categoryRepo = app(CategoryRepository::class);
$productRepo  = app(ProductRepository::class);

$channel   = core()->getCurrentChannel();
$localeCode = $channel->default_locale->code ?? 'en';
app()->setLocale($localeCode);
$localeId  = DB::table('locales')->where('code', $localeCode)->value('id');
$rootId    = $channel->root_category_id ?: 1;

out("channel={$channel->code} locale={$localeCode} root={$rootId}");

/* ---------- helper: download an image to a disk path ---------- */
function fetchImage(string $keywords, string $diskPath): bool {
    $url = "https://loremflickr.com/700/880/".rawurlencode($keywords)."?lock=".abs(crc32($diskPath));
    $bin = @file_get_contents($url);
    if ($bin === false || strlen($bin) < 2000) {
        // fallback to deterministic placeholder
        $bin = @file_get_contents("https://picsum.photos/seed/".abs(crc32($diskPath))."/700/880");
    }
    if ($bin === false || strlen($bin) < 1000) return false;
    Storage::disk('public')->put($diskPath, $bin);
    return true;
}

/* ==================================================================
   CATEGORIES
   ================================================================== */
$categories = [
    ['name' => 'Shirts',    'slug' => 'shirts',    'kw' => 'mens,shirt',          'desc' => 'Tailored and casual shirts in natural fibres.'],
    ['name' => 'T-Shirts',  'slug' => 't-shirts',  'kw' => 'tshirt,clothing',     'desc' => 'Everyday tees in soft, breathable cotton.'],
    ['name' => 'Kurtas',    'slug' => 'kurtas',    'kw' => 'kurta,indian,clothing','desc' => 'Handcrafted ethnic kurtas for every occasion.'],
    ['name' => 'Fabrics',   'slug' => 'fabrics',   'kw' => 'fabric,textile,cloth','desc' => 'Handloom cloth sold by the metre.'],
];

$catIds = [];
$pos = 1;
foreach ($categories as $c) {
    $existing = DB::table('category_translations')->where('slug', $c['slug'])->value('category_id');
    if ($existing) {
        $catIds[$c['slug']] = $existing;
        if (! DB::table('categories')->where('id', $existing)->value('logo_path')) {
            $imgPath = "category/$existing/".$c['slug'].".jpg";
            if (fetchImage($c['kw'], $imgPath)) {
                DB::table('categories')->where('id', $existing)->update(['logo_path' => $imgPath]);
            }
        }
        out("category '{$c['slug']}' exists (#$existing) — reuse (+image)");
        $pos++;
        continue;
    }
    try {
        $category = $categoryRepo->create([
            'parent_id'    => $rootId,
            'status'       => 1,
            'position'     => $pos,
            'display_mode' => 'products_and_description',
            $localeCode    => [
                'name'        => $c['name'],
                'slug'        => $c['slug'],
                'description' => $c['desc'],
                'locale_id'   => $localeId,
            ],
        ]);
        $catIds[$c['slug']] = $category->id;
        out("category '{$c['slug']}' created #{$category->id}");
    } catch (\Throwable $e) {
        out("CATEGORY ERROR '{$c['slug']}': ".$e->getMessage());
        $pos++;
        continue;
    }
    try {
        $imgPath = "category/{$catIds[$c['slug']]}/".$c['slug'].".jpg";
        if (fetchImage($c['kw'], $imgPath)) {
            DB::table('categories')->where('id', $catIds[$c['slug']])->update(['logo_path' => $imgPath]);
        }
    } catch (\Throwable $e) { out("  cat image warn: ".$e->getMessage()); }
    $pos++;
}

/* ==================================================================
   PRODUCTS
   ================================================================== */
$products = [
    ['sku'=>'wfs-shirt-oxford',  'name'=>'Oxford Cotton Shirt',    'price'=>1499, 'special'=>null, 'cat'=>'shirts',   'kw'=>'oxford,shirt',          'new'=>1,'feat'=>1,'sd'=>'Crisp button-down in pure Oxford cotton.'],
    ['sku'=>'wfs-shirt-linen',   'name'=>'Linen Summer Shirt',     'price'=>1899, 'special'=>1499, 'cat'=>'shirts',   'kw'=>'linen,shirt',           'new'=>1,'feat'=>0,'sd'=>'Breathable pure-linen shirt for warm days.'],
    ['sku'=>'wfs-shirt-flannel', 'name'=>'Checked Flannel Shirt',  'price'=>1699, 'special'=>null, 'cat'=>'shirts',   'kw'=>'flannel,shirt,checked', 'new'=>0,'feat'=>1,'sd'=>'Brushed flannel in a heritage check.'],
    ['sku'=>'wfs-tee-crew',      'name'=>'Classic Crew Tee',       'price'=>699,  'special'=>null, 'cat'=>'t-shirts', 'kw'=>'tshirt,plain',          'new'=>1,'feat'=>1,'sd'=>'The everyday crew-neck in combed cotton.'],
    ['sku'=>'wfs-tee-pima',      'name'=>'Pima Cotton Tee',        'price'=>899,  'special'=>699,  'cat'=>'t-shirts', 'kw'=>'tshirt,cotton',         'new'=>0,'feat'=>0,'sd'=>'Buttery-soft Pima cotton, built to last.'],
    ['sku'=>'wfs-tee-henley',    'name'=>'Striped Henley Tee',     'price'=>999,  'special'=>null, 'cat'=>'t-shirts', 'kw'=>'henley,tshirt,striped', 'new'=>1,'feat'=>0,'sd'=>'A three-button henley with fine stripes.'],
    ['sku'=>'wfs-kurta-cotton',  'name'=>'Handloom Cotton Kurta',  'price'=>1299, 'special'=>null, 'cat'=>'kurtas',   'kw'=>'kurta,cotton',          'new'=>1,'feat'=>1,'sd'=>'Straight-cut kurta in handloom cotton.'],
    ['sku'=>'wfs-kurta-silk',    'name'=>'Festive Silk Kurta',     'price'=>2499, 'special'=>1999, 'cat'=>'kurtas',   'kw'=>'silk,kurta,festive',    'new'=>0,'feat'=>1,'sd'=>'Lustrous silk kurta for celebrations.'],
    ['sku'=>'wfs-kurta-khadi',   'name'=>'Khadi Nehru Kurta',      'price'=>1599, 'special'=>null, 'cat'=>'kurtas',   'kw'=>'khadi,kurta',           'new'=>1,'feat'=>0,'sd'=>'Hand-spun khadi with a Nehru collar.'],
    ['sku'=>'wfs-fab-cotton',    'name'=>'Handloom Cotton Fabric', 'price'=>540,  'special'=>null, 'cat'=>'fabrics',  'kw'=>'cotton,fabric,textile', 'new'=>0,'feat'=>1,'sd'=>'Naturally dyed cotton, sold per metre.'],
    ['sku'=>'wfs-fab-indigo',    'name'=>'Indigo Linen Fabric',    'price'=>720,  'special'=>590,  'cat'=>'fabrics',  'kw'=>'indigo,linen,fabric',   'new'=>1,'feat'=>1,'sd'=>'Vat-dyed indigo linen by the metre.'],
    ['sku'=>'wfs-fab-silk',      'name'=>'Tussar Silk Fabric',     'price'=>980,  'special'=>null, 'cat'=>'fabrics',  'kw'=>'silk,fabric,textile',   'new'=>1,'feat'=>0,'sd'=>'Wild tussar silk with a natural slub.'],
];

$created = 0;
foreach ($products as $p) {
    $existsId = DB::table('products')->where('sku', $p['sku'])->value('id');
    if ($existsId) {
        try { \Webkul\Product\Models\Product::find($existsId)?->delete(); out("product '{$p['sku']}' existed — deleted #$existsId, recreating"); }
        catch (\Throwable $e) { out("  delete warn: ".$e->getMessage()); }
    }

    try {
        $product = $productRepo->create([
            'type'                => 'simple',
            'attribute_family_id' => 1,
            'sku'                 => $p['sku'],
        ]);

        $data = [
            'channel'               => $channel->code,
            'locale'                => $localeCode,
            'sku'                   => $p['sku'],
            'name'                  => $p['name'],
            'url_key'               => \Illuminate\Support\Str::slug($p['name']),
            'short_description'     => '<p>'.$p['sd'].'</p>',
            'description'           => '<p>'.$p['sd'].' Crafted by Weavers Fab Studio from naturally sourced fibres, finished and inspected by hand.</p>',
            'price'                 => $p['price'],
            'special_price'         => $p['special'],
            'special_price_from'    => null,
            'special_price_to'      => null,
            'weight'                => 0.3,
            'status'                => 1,
            'new'                   => $p['new'],
            'featured'              => $p['feat'],
            'visible_individually'  => 1,
            'guest_checkout'        => 1,
            'product_number'        => strtoupper($p['sku']),
            'manage_stock'          => 1,
            'categories'            => [$catIds[$p['cat']]],
            'channels'              => [$channel->id],
            'inventories'           => [1 => rand(40, 120)],
        ];

        $productRepo->update($data, $product->id);

        // image(s)
        $imgPath = "product/{$product->id}/".$p['sku'].".jpg";
        if (fetchImage($p['kw'], $imgPath)) {
            ProductImage::create([
                'product_id' => $product->id,
                'path'       => $imgPath,
                'position'   => 1,
            ]);
        }

        $created++;
        out("product '{$p['sku']}' created #{$product->id}  (img: ".($imgPath).")");
    } catch (\Throwable $e) {
        out("PRODUCT ERROR '{$p['sku']}': ".$e->getMessage()." @ ".$e->getFile().":".$e->getLine());
    }
}

out("---- created $created products. running indexer ----");
try {
    \Illuminate\Support\Facades\Artisan::call('indexer:index', ['--type' => null]);
    out(trim(\Illuminate\Support\Facades\Artisan::output()));
} catch (\Throwable $e) {
    out("INDEXER ERROR: ".$e->getMessage());
}
out("DONE.");
