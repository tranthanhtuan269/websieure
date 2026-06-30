(function () {
  'use strict';

  function pad(n) { return n < 10 ? '0' + n : String(n); }

  /* Hero slider */
  function initHero() {
    var slides = document.querySelectorAll('.kf-hero__slide');
    var dots = document.querySelectorAll('.kf-hero__dot');
    if (!slides.length) return;

    var cur = 0;
    function go(i) {
      slides[cur].classList.remove('kf-hero__slide--active');
      if (dots[cur]) dots[cur].classList.remove('kf-hero__dot--active');
      cur = i;
      slides[cur].classList.add('kf-hero__slide--active');
      if (dots[cur]) dots[cur].classList.add('kf-hero__dot--active');
    }

    dots.forEach(function (d) {
      d.addEventListener('click', function () { go(parseInt(d.getAttribute('data-i'), 10)); });
    });

    setInterval(function () { go((cur + 1) % slides.length); }, 5000);
  }

  /* Countdown */
  function initCountdown() {
    var el = document.getElementById('kf-countdown');
    if (!el) return;
    var end = new Date();
    end.setHours(23, 59, 59, 0);

    function tick() {
      var diff = Math.max(0, end - new Date());
      el.querySelector('[data-h]').textContent = pad(Math.floor(diff / 3600000));
      el.querySelector('[data-m]').textContent = pad(Math.floor((diff % 3600000) / 60000));
      el.querySelector('[data-s]').textContent = pad(Math.floor((diff % 60000) / 1000));
    }
    tick();
    setInterval(tick, 1000);
  }

  /* Tabs */
  function initTabs() {
    document.querySelectorAll('[data-tabs]').forEach(function (wrap) {
      var btns = wrap.querySelectorAll('.kf-tabs__btn');
      var section = wrap.closest('.kf-split-section') || wrap.parentElement;
      if (!section) return;
      var panels = section.querySelectorAll('.kf-tab-panel');

      btns.forEach(function (btn) {
        btn.addEventListener('click', function () {
          var tab = btn.getAttribute('data-tab');
          btns.forEach(function (b) { b.classList.remove('kf-tabs__btn--active'); });
          btn.classList.add('kf-tabs__btn--active');
          if (!tab) return;
          panels.forEach(function (p) {
            p.classList.toggle('kf-tab-panel--active', p.getAttribute('data-panel') === tab);
          });
        });
      });
    });
  }

  /* Carousels */
  function initCarousels() {
    document.querySelectorAll('.kf-carousel__arrow').forEach(function (btn) {
      btn.addEventListener('click', function () {
        var id = btn.getAttribute('data-target');
        var track = document.getElementById(id);
        if (!track) return;
        var amount = track.offsetWidth * 0.7;
        track.scrollBy({ left: btn.classList.contains('kf-carousel__arrow--prev') ? -amount : amount, behavior: 'smooth' });
      });
    });
  }

  /* Drawer */
  function initDrawer() {
    var drawer = document.getElementById('kf-drawer');
    var open = document.getElementById('kf-menu-btn');
    var close = document.getElementById('kf-drawer-close');
    if (!drawer || !open) return;
    open.addEventListener('click', function () { drawer.classList.add('open'); });
    if (close) close.addEventListener('click', function () { drawer.classList.remove('open'); });
    drawer.addEventListener('click', function (e) { if (e.target === drawer) drawer.classList.remove('open'); });
  }

  /* Scroll top */
  function initScrollTop() {
    var btn = document.getElementById('kf-scroll-top');
    if (!btn) return;
    window.addEventListener('scroll', function () { btn.classList.toggle('visible', window.scrollY > 400); });
    btn.addEventListener('click', function () { window.scrollTo({ top: 0, behavior: 'smooth' }); });
  }

  document.addEventListener('DOMContentLoaded', function () {
    initHero();
    initCountdown();
    initTabs();
    initCarousels();
    initDrawer();
    initScrollTop();
  });
})();
