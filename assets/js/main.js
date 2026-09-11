(function () {
  'use strict';

  // Mobile menu toggle
  var toggle = document.querySelector('.menu-toggle');
  var menu = document.querySelector('.menu');
  if (toggle && menu) {
    toggle.addEventListener('click', function () {
      var open = menu.classList.toggle('is-open');
      toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
    });
  }

  // Split-section hover crossfade (People / Events on the homepage)
  document.querySelectorAll('.split-section').forEach(function (section) {
    var links = section.querySelectorAll('.name-link');
    var images = section.querySelectorAll('.visual-image');

    function activate(index) {
      links.forEach(function (l) { l.classList.toggle('is-active', l.dataset.index === String(index)); });
      images.forEach(function (img) { img.classList.toggle('is-active', img.dataset.index === String(index)); });
    }

    links.forEach(function (link) {
      link.addEventListener('mouseenter', function () { activate(link.dataset.index); });
      link.addEventListener('focus', function () { activate(link.dataset.index); });
    });
  });

  // Lightbox on gallery pages
  var lightbox = document.querySelector('.lightbox');
  if (lightbox && window.__GALLERY__) {
    var items = window.__GALLERY__;
    var img = lightbox.querySelector('.lightbox-image');
    var current = 0;

    function show(index) {
      current = (index + items.length) % items.length;
      img.src = items[current].full;
      img.alt = items[current].caption || '';
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
