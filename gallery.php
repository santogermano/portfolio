<?php
declare(strict_types=1);
require __DIR__ . '/inc/functions.php';

$type = ($_GET['type'] ?? '') === 'events' ? 'events' : 'people';
$slug = basename((string) ($_GET['slug'] ?? ''));
$entry = get_entry($type, $slug);

if (!$entry) {
    http_response_code(404);
    $pageTitle = 'Not found';
    require __DIR__ . '/inc/header.php';
    echo '<p class="empty-state">Nothing here yet.</p>';
    require __DIR__ . '/inc/footer.php';
    exit;
}

$pageTitle = $entry['name'] . ' — Photography';
$activeType = $type;
require __DIR__ . '/inc/header.php';
?>
<section class="gallery-header">
  <a class="back-link" href="/#<?= htmlspecialchars($type) ?>">&larr; Back</a>
  <h1><?= htmlspecialchars($entry['name']) ?></h1>
  <?php if ($entry['bio']): ?><p class="bio"><?= htmlspecialchars($entry['bio']) ?></p><?php endif; ?>
</section>

<section class="gallery-grid">
  <?php foreach ($entry['images'] as $i => $file): ?>
  <figure class="gallery-item">
    <a href="<?= content_url($type, $slug, $file) ?>" data-lightbox data-index="<?= $i ?>">
      <img src="<?= thumb_url($type, $slug, $file, 900) ?>"
           alt="<?= htmlspecialchars($entry['captions'][$file] ?? $entry['name']) ?>" loading="lazy">
      <?php if (!empty($entry['captions'][$file])): ?>
      <figcaption><?= htmlspecialchars($entry['captions'][$file]) ?></figcaption>
      <?php endif; ?>
    </a>
  </figure>
  <?php endforeach; ?>
</section>

<div class="lightbox" hidden>
  <button class="lightbox-close" aria-label="Close">&times;</button>
  <button class="lightbox-prev" aria-label="Previous">&lsaquo;</button>
  <img class="lightbox-image" src="" alt="">
  <button class="lightbox-next" aria-label="Next">&rsaquo;</button>
</div>

<script>
  window.__GALLERY__ = <?= json_encode(array_map(
      fn($file) => ['full' => content_url($type, $slug, $file), 'caption' => $entry['captions'][$file] ?? ''],
      $entry['images']
  )) ?>;
</script>
<?php require __DIR__ . '/inc/footer.php'; ?>
