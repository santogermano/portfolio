(function () {
  'use strict';

  window.addEventListener('load', function () {
    document.body.classList.remove('is-loading');
    document.body.classList.add('is-loaded');
  });

  // Mobile menu toggle
  var toggle = document.querySelector('.menu-toggle');
  var menu = document.querySelector('.menu');
  if (toggle && menu) {
    toggle.addEventListener('click', function () {
      var open = menu.classList.toggle('is-open');
      toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
    });
  }

  // Custom cursor
  var cursor = document.querySelector('.cursor-dot');
  if (cursor && window.matchMedia('(hover: hover) and (pointer: fine)').matches) {
    var cx = 0, cy = 0;
    window.addEventListener('mousemove', function (e) {
      cx = e.clientX; cy = e.clientY;
      cursor.style.transform = 'translate3d(' + cx + 'px,' + cy + 'px,0)';
      cursor.classList.add('is-visible');
    });
    document.addEventListener('mouseleave', function () { cursor.classList.remove('is-visible'); });
    var hoverTargets = 'a, button, .gallery-item, .name-link';
    document.addEventListener('mouseover', function (e) {
      if (e.target.closest(hoverTargets)) cursor.classList.add('is-hover');
    });
    document.addEventListener('mouseout', function (e) {
      if (e.target.closest(hoverTargets)) cursor.classList.remove('is-hover');
    });
  }

  // Scroll reveal
  var revealTargets = document.querySelectorAll('.reveal');
  if (revealTargets.length && 'IntersectionObserver' in window) {
    var io = new IntersectionObserver(function (entries) {
      entries.forEach(function (entry) {
        if (entry.isIntersecting) {
          entry.target.classList.add('in-view');
          io.unobserve(entry.target);
        }
      });
    }, { threshold: 0.12, rootMargin: '0px 0px -60px 0px' });
    revealTargets.forEach(function (el) { io.observe(el); });
  } else {
    revealTargets.forEach(function (el) { el.classList.add('in-view'); });
  }

  // Split-section hover crossfade (People / Events on the homepage) + subtle parallax
  document.querySelectorAll('.split-section').forEach(function (section) {
    var links = section.querySelectorAll('.name-link');
    var images = section.querySelectorAll('.visual-image');
    var captions = section.querySelectorAll('.visual-caption');
    var visual = section.querySelector('.split-visual');

    function activate(index) {
      links.forEach(function (l) { l.classList.toggle('is-active', l.dataset.index === String(index)); });
      images.forEach(function (img) { img.classList.toggle('is-active', img.dataset.index === String(index)); });
      captions.forEach(function (c) { c.classList.toggle('is-active', c.dataset.index === String(index)); });
    }

    links.forEach(function (link) {
      link.addEventListener('mouseenter', function () { activate(link.dataset.index); });
      link.addEventListener('focus', function () { activate(link.dataset.index); });
    });

    if (visual) {
      visual.addEventListener('mousemove', function (e) {
        var rect = visual.getBoundingClientRect();
        var px = (e.clientX - rect.left) / rect.width - 0.5;
        var py = (e.clientY - rect.top) / rect.height - 0.5;
        var active = visual.querySelector('.visual-image.is-active img');
        if (active) {
          active.style.transform = 'scale(1.0) translate(' + (px * -14) + 'px,' + (py * -14) + 'px)';
        }
      });
      visual.addEventListener('mouseleave', function () {
        var active = visual.querySelector('.visual-image.is-active img');
        if (active) active.style.transform = '';
      });
    }
  });

  // Masonry-style grid: give each item a row-span based on its rendered aspect ratio
  var grid = document.querySelector('.gallery-grid');
  if (grid) {
    var rowHeight = parseInt(getComputedStyle(grid).getPropertyValue('grid-auto-rows'), 10) || 6;
    var rowGap = parseInt(getComputedStyle(grid).getPropertyValue('gap'), 10) || 4;

    function layout() {
      grid.querySelectorAll('.gallery-item').forEach(function (item) {
        var img = item.querySelector('img');
        if (!img || !img.naturalWidth) return;
        var width = item.getBoundingClientRect().width;
        var renderedHeight = width * (img.naturalHeight / img.naturalWidth);
        var span = Math.ceil((renderedHeight + rowGap) / (rowHeight + rowGap));
        item.style.gridRowEnd = 'span ' + span;
      });
    }

    grid.querySelectorAll('img').forEach(function (img) {
      if (img.complete) return;
      img.addEventListener('load', layout);
    });
    window.addEventListener('load', layout);
    window.addEventListener('resize', layout);
    layout();
  }

  // Lightbox on gallery pages
  var lightbox = document.querySelector('.lightbox');
  if (lightbox && window.__GALLERY__) {
    var items = window.__GALLERY__;
    var img = lightbox.querySelector('.lightbox-image');
    var current = 0;

    function show(index) {
      current = (index + items.length) % items.length;
      img.classList.remove('is-shown');
      var next = new Image();
      next.onload = function () {
        img.src = items[current].full;
        img.alt = items[current].caption || '';
        requestAnimationFrame(function () { img.classList.add('is-shown'); });
      };
      next.src = items[current].full;
    }

    function open(index) {
      show(index);
      lightbox.hidden = false;
      document.body.style.overflow = 'hidden';
    }

    function close() {
      lightbox.hidden = true;
      document.body.style.overflow = '';
    }

    document.querySelectorAll('[data-lightbox]').forEach(function (link) {
      link.addEventListener('click', function (e) {
        e.preventDefault();
        open(parseInt(link.dataset.index, 10));
      });
    });

    lightbox.querySelector('.lightbox-close').addEventListener('click', close);
    lightbox.querySelector('.lightbox-prev').addEventListener('click', function () { show(current - 1); });
    lightbox.querySelector('.lightbox-next').addEventListener('click', function () { show(current + 1); });
    lightbox.addEventListener('click', function (e) { if (e.target === lightbox) close(); });

    document.addEventListener('keydown', function (e) {
      if (lightbox.hidden) return;
      if (e.key === 'Escape') close();
      if (e.key === 'ArrowLeft') show(current - 1);
      if (e.key === 'ArrowRight') show(current + 1);
    });
  }
})();
