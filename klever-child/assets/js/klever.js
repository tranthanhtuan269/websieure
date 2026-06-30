(function () {
  'use strict';

  function initCountdown() {
    var el = document.getElementById('klever-countdown');
    if (!el) return;

    var end = new Date();
    end.setDate(end.getDate() + 5);
    end.setHours(23, 59, 59, 0);

    function pad(n) { return n < 10 ? '0' + n : String(n); }

    function tick() {
      var now = new Date();
      var diff = Math.max(0, end - now);
      var days = Math.floor(diff / 86400000);
      var hours = Math.floor((diff % 86400000) / 3600000);
      var mins = Math.floor((diff % 3600000) / 60000);
      var secs = Math.floor((diff % 60000) / 1000);

      el.querySelector('[data-days]').textContent = days;
      el.querySelector('[data-hours]').textContent = pad(hours);
      el.querySelector('[data-mins]').textContent = pad(mins);
      el.querySelector('[data-secs]').textContent = pad(secs);
    }

    tick();
    setInterval(tick, 1000);
  }

  function initDrawer() {
    var drawer = document.getElementById('klever-drawer');
    var openBtn = document.getElementById('klever-menu-btn');
    var closeBtn = document.getElementById('klever-drawer-close');
    if (!drawer || !openBtn) return;

    openBtn.addEventListener('click', function () { drawer.classList.add('open'); });
    if (closeBtn) closeBtn.addEventListener('click', function () { drawer.classList.remove('open'); });
    drawer.addEventListener('click', function (e) {
      if (e.target === drawer) drawer.classList.remove('open');
    });
  }

  function initHeroSlider() {
    var slides = document.querySelectorAll('.klever-hero__slide');
    var dots = document.querySelectorAll('.klever-hero__dot');
    if (slides.length < 2) return;

    var current = 0;
    slides.forEach(function (s, i) { s.style.display = i === 0 ? 'block' : 'none'; });

    setInterval(function () {
      slides[current].style.display = 'none';
      if (dots[current]) dots[current].classList.remove('klever-hero__dot--active');
      current = (current + 1) % slides.length;
      slides[current].style.display = 'block';
      if (dots[current]) dots[current].classList.add('klever-hero__dot--active');
    }, 5000);
  }

  document.addEventListener('DOMContentLoaded', function () {
    initCountdown();
    initDrawer();
    initHeroSlider();
  });
})();
