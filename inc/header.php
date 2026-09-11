<?php
/** @var string $pageTitle */
/** @var string|null $activeType */
$people = get_collection('people');
$events = get_collection('events');
$activeType = $activeType ?? null;
?><!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= htmlspecialchars($pageTitle ?? 'Photography') ?></title>
<meta name="description" content="Photography portfolio.">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,300;0,9..144,400;0,9..144,500;0,9..144,600;1,9..144,400;1,9..144,500&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="/assets/css/style.css">
<link rel="icon" href="data:,">
</head>
<body class="is-loading">
<div class="grain" aria-hidden="true"></div>
<div class="cursor-dot" aria-hidden="true"></div>
<header class="topbar">
  <a class="brand" href="/"><span class="brand-mark">&#9670;</span> Photography</a>
  <nav class="menu">
    <div class="menu-item <?= $activeType === 'people' ? 'is-active' : '' ?>">
      <a href="/#people">People</a>
      <?php if ($people): ?>
      <ul class="submenu">
        <?php foreach ($people as $p): ?>
        <li><a href="<?= permalink('people', $p['slug']) ?>"><?= htmlspecialchars($p['name']) ?></a></li>
        <?php endforeach; ?>
      </ul>
      <?php endif; ?>
    </div>
    <div class="menu-item <?= $activeType === 'events' ? 'is-active' : '' ?>">
      <a href="/#events">Events</a>
      <?php if ($events): ?>
      <ul class="submenu">
        <?php foreach ($events as $e): ?>
        <li><a href="<?= permalink('events', $e['slug']) ?>"><?= htmlspecialchars($e['name']) ?></a></li>
        <?php endforeach; ?>
      </ul>
      <?php endif; ?>
    </div>
  </nav>
  <button class="menu-toggle" aria-label="Toggle menu" aria-expanded="false">Menu</button>
</header>
<main>
