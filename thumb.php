<?php
declare(strict_types=1);
require __DIR__ . '/inc/functions.php';

$type = $_GET['type'] ?? '';
$slug = basename((string) ($_GET['slug'] ?? ''));
$file = basename((string) ($_GET['file'] ?? ''));
$width = max(50, min(2400, (int) ($_GET['w'] ?? 800)));

$type = $type === 'events' ? 'events' : 'people';
$source = collection_root($type) . '/' . $slug . '/' . $file;

if (!is_file($source)) {
    http_response_code(404);
    exit('Not found');
}

$cacheDir = __DIR__ . '/cache/thumbs/' . $type . '/' . $slug;
$cacheFile = $cacheDir . '/' . $width . '-' . $file;

if (is_file($cacheFile) && filemtime($cacheFile) >= filemtime($source)) {
    serve($cacheFile);
}

if (!is_dir($cacheDir)) {
    mkdir($cacheDir, 0755, true);
}

$info = getimagesize($source);
if (!$info) {
    serve($source);
}
[$srcWidth, $srcHeight, $imageType] = $info;

if ($srcWidth <= $width) {
    // Already small enough — just cache a copy so future requests are fast.
    copy($source, $cacheFile);
    serve($cacheFile);
}

$image = match ($imageType) {
    IMAGETYPE_JPEG => imagecreatefromjpeg($source),
    IMAGETYPE_PNG => imagecreatefrompng($source),
    IMAGETYPE_WEBP => imagecreatefromwebp($source),
    default => null,
};

if (!$image) {
    serve($source);
}

$height = (int) round($srcHeight * ($width / $srcWidth));
$resized = imagecreatetruecolor($width, $height);

if ($imageType === IMAGETYPE_PNG) {
    imagealphablending($resized, false);
    imagesavealpha($resized, true);
}

imagecopyresampled($resized, $image, 0, 0, 0, 0, $width, $height, $srcWidth, $srcHeight);

match ($imageType) {
    IMAGETYPE_JPEG => imagejpeg($resized, $cacheFile, 82),
    IMAGETYPE_PNG => imagepng($resized, $cacheFile, 6),
    IMAGETYPE_WEBP => imagewebp($resized, $cacheFile, 82),
    default => copy($source, $cacheFile),
};

imagedestroy($image);
imagedestroy($resized);

serve($cacheFile);

function serve(string $path): void
{
    $mime = match (strtolower(pathinfo($path, PATHINFO_EXTENSION))) {
        'png' => 'image/png',
        'webp' => 'image/webp',
        default => 'image/jpeg',
    };
    header('Content-Type: ' . $mime);
    header('Cache-Control: public, max-age=2592000, immutable');
    header('Content-Length: ' . filesize($path));
    readfile($path);
    exit;
}
