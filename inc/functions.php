<?php
declare(strict_types=1);

define('CONTENT_ROOT', __DIR__ . '/../content');
define('IMAGE_EXTENSIONS', ['jpg', 'jpeg', 'png', 'webp']);

function slugify(string $text): string
{
    $text = strtolower(trim($text));
    $text = preg_replace('/[^a-z0-9]+/', '-', $text);
    return trim($text, '-');
}

function titleize(string $slug): string
{
    return ucwords(str_replace('-', ' ', $slug));
}

/**
 * A "collection" is either people or events. Each is a folder of subfolders,
 * one subfolder per person/event, discovered from the filesystem — adding a
 * folder (with photos in it) is enough to make it appear on the site.
 */
function collection_root(string $type): string
{
    $type = $type === 'events' ? 'events' : 'people';
    return CONTENT_ROOT . '/' . $type;
}

function list_images(string $dir): array
{
    if (!is_dir($dir)) {
        return [];
    }
    $files = [];
    foreach (scandir($dir) as $file) {
        if ($file === '.' || $file === '..') {
            continue;
        }
        $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
        if (in_array($ext, IMAGE_EXTENSIONS, true) && stripos($file, 'cover.') !== 0) {
            $files[] = $file;
        }
    }
    sort($files, SORT_NATURAL | SORT_FLAG_CASE);
    return $files;
}

function find_cover(string $dir, array $images): ?string
{
    foreach (IMAGE_EXTENSIONS as $ext) {
        if (is_file("$dir/cover.$ext")) {
            return "cover.$ext";
        }
    }
    return $images[0] ?? null;
}

function read_meta(string $dir): array
{
    $path = "$dir/meta.json";
    if (!is_file($path)) {
        return [];
    }
    $json = json_decode((string) file_get_contents($path), true);
    return is_array($json) ? $json : [];
}

/**
 * Returns every entry in a collection (people or events), sorted for display.
 * Each entry: slug, name, bio, order, cover (filename or null), images (array).
 */
function get_collection(string $type): array
{
    $root = collection_root($type);
    if (!is_dir($root)) {
        return [];
    }

    $entries = [];
    foreach (scandir($root) as $slug) {
        if ($slug === '.' || $slug === '..') {
            continue;
        }
        $dir = "$root/$slug";
        if (!is_dir($dir)) {
            continue;
        }
        $meta = read_meta($dir);
        $images = list_images($dir);
        $coverFile = $meta['cover'] ?? find_cover($dir, $images);
        if (!$coverFile && !count($images)) {
            continue; // empty folder, nothing to show yet
        }
        $entries[] = [
            'slug' => $slug,
            'name' => $meta['name'] ?? titleize($slug),
            'bio' => $meta['bio'] ?? '',
            'order' => $meta['order'] ?? PHP_INT_MAX,
            'cover' => $coverFile,
            'images' => $images,
            'captions' => $meta['captions'] ?? [],
        ];
    }

    usort($entries, function ($a, $b) {
        return $a['order'] <=> $b['order'] ?: strcasecmp($a['name'], $b['name']);
    });

    return $entries;
}

function get_entry(string $type, string $slug): ?array
{
    foreach (get_collection($type) as $entry) {
        if ($entry['slug'] === $slug) {
            return $entry;
        }
    }
    return null;
}

function content_url(string $type, string $slug, string $file): string
{
    $type = $type === 'events' ? 'events' : 'people';
    return '/content/' . $type . '/' . rawurlencode($slug) . '/' . rawurlencode($file);
}

function thumb_url(string $type, string $slug, string $file, int $width): string
{
    return '/thumb.php?type=' . urlencode($type) . '&slug=' . urlencode($slug)
        . '&file=' . urlencode($file) . '&w=' . $width;
}

function permalink(string $type, string $slug): string
{
    return '/' . ($type === 'events' ? 'events' : 'people') . '/' . rawurlencode($slug) . '/';
}
