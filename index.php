<?php
declare(strict_types=1);
require __DIR__ . '/inc/functions.php';

$pageTitle = 'Photography';
$people = get_collection('people');
$events = get_collection('events');

function render_section(string $type, string $label, array $entries): void
{
    if (!$entries) {
        return;
    }
    ?>
    <section class="split-section" id="<?= htmlspecialchars($type) ?>" data-type="<?= htmlspecialchars($type) ?>">
      <div class="split-names">
        <h2 class="section-label"><?= htmlspecialchars($label) ?></h2>
        <ul class="name-list">
          <?php foreach ($entries as $i => $entry): ?>
          <li>
            <a href="<?= permalink($type, $entry['slug']) ?>"
               class="name-link<?= $i === 0 ? ' is-active' : '' ?>"
               data-index="<?= $i ?>">
              <?= htmlspecialchars($entry['name']) ?>
            </a>
          </li>
          <?php endforeach; ?>
        </ul>
      </div>
      <div class="split-visual">
        <?php foreach ($entries as $i => $entry): ?>
          <?php if ($entry['cover']): ?>
          <a href="<?= permalink($type, $entry['slug']) ?>"
             class="visual-image<?= $i === 0 ? ' is-active' : '' ?>"
             data-index="<?= $i ?>">
            <img src="<?= thumb_url($type, $entry['slug'], $entry['cover'], 1400) ?>"
                 alt="<?= htmlspecialchars($entry['name']) ?>" loading="<?= $i === 0 ? 'eager' : 'lazy' ?>">
          </a>
          <?php endif; ?>
        <?php endforeach; ?>
        <?php foreach ($entries as $i => $entry): ?>
        <span class="visual-caption<?= $i === 0 ? ' is-active' : '' ?>" data-index="<?= $i ?>"><?= htmlspecialchars($entry['name']) ?></span>
        <?php endforeach; ?>
      </div>
    </section>
    <?php
}

require __DIR__ . '/inc/header.php';
?>
<section class="hero reveal">
  <span class="hero-eyebrow">Photography Studio</span>
  <h1>A visual archive<br>of <em>people</em> &amp; moments.</h1>
</section>

<?php
render_section('people', 'People', $people);
render_section('events', 'Events', $events);

if (!$people && !$events) {
    echo '<p class="empty-state">No people or events published yet. Drop photos into <code>content/people/&lt;name&gt;/</code> or <code>content/events/&lt;name&gt;/</code> to get started — see README.md.</p>';
}

require __DIR__ . '/inc/footer.php';
