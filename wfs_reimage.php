<?php
/** Replace catalog images with curated Unsplash apparel photos. Run: php wfs_reimage.php */
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

function out($m){ echo $m.PHP_EOL; }
function wfs_dl(string $id, int $w, int $h, string $path): bool {
    $url = "https://images.unsplash.com/photo-$id?w=$w&h=$h&fit=crop&crop=entropy&q=80";
    $bin = @file_get_contents($url);
    if ($bin === false || strlen($bin) < 3000) return false;
    Storage::disk('public')->put($path, $bin);
    return true;
}

/* curated Unsplash photo IDs (verified apparel) */
$catImg = [
    'shirts'   => '1602810318383-e386cc2a3ccf', // folded shirts
    't-shirts' => '1521572163474-6864f9cf17ab', // white tee
    'kurtas'   => '1434389677669-e08b4cac3105', // cream knit / ethnic
    'fabrics'  => '1542272604-787c3835535d',     // folded denim / cloth
];
$prodImg = [
    'wfs-shirt-oxford'  => '1598033129183-c4f50c736f10', // white shirt + tie
    'wfs-shirt-linen'   => '1593757147298-e064ed1419e5', // linen shirt
    'wfs-shirt-flannel' => '1607345366928-199ea26cfe3e', // flannel check
    'wfs-tee-crew'      => '1581655353564-df123a1eb820', // white tee hanger
    'wfs-tee-pima'      => '1618517351616-38fb9c5210c6', // black tee
    'wfs-tee-henley'    => '1571945153237-4929e783af4a', // printed tee
    'wfs-kurta-cotton'  => '1620799140408-edc6dcb6d633', // natural flatlay
    'wfs-kurta-silk'    => '1604644401890-0bd678c83788', // red garments
    'wfs-kurta-khadi'   => '1564859228273-274232fdb516', // white tee back
    'wfs-fab-cotton'    => '1556905055-8f358a7a47b2',     // folded clothes flatlay
    'wfs-fab-indigo'    => '1620012253295-c15cc3e65df4', // blue / indigo
    'wfs-fab-silk'      => '1551232864-3f0890e580d9',     // garment rack
];

/* categories */
foreach ($catImg as $slug => $id) {
    $catId = DB::table('category_translations')->where('slug', $slug)->value('category_id');
    if (! $catId) { out("cat '$slug' missing"); continue; }
    $path = "category/$catId/$slug.jpg";
    if (wfs_dl($id, 900, 1100, $path)) {
        DB::table('categories')->where('id', $catId)->update(['logo_path' => $path]);
        out("category '$slug' image -> $id");
    } else out("category '$slug' DL FAILED");
}

/* products */
foreach ($prodImg as $sku => $id) {
    $pid = DB::table('products')->where('sku', $sku)->value('id');
    if (! $pid) { out("product '$sku' missing"); continue; }
    $path = "product/$pid/$sku.jpg";
    if (wfs_dl($id, 800, 1000, $path)) {
        // ensure a product_images row exists
        if (! DB::table('product_images')->where('product_id', $pid)->where('path', $path)->exists()) {
            DB::table('product_images')->insert(['product_id'=>$pid,'path'=>$path,'position'=>1]);
        }
        out("product '$sku' image -> $id");
    } else out("product '$sku' DL FAILED");
}

/* bust the resized image cache so new originals are served */
foreach (['cache', 'storage/app/public/cache', 'public/cache'] as $dir) {
    $full = __DIR__.'/'.$dir;
    if (is_dir($full)) {
        $it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($full, FilesystemIterator::SKIP_DOTS), RecursiveIteratorIterator::CHILD_FIRST);
        foreach ($it as $f) { $f->isDir() ? @rmdir($f) : @unlink($f); }
        out("cleared cache dir: $dir");
    }
}
out("DONE.");
