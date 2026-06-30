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

  function initCouponCopy() {
    document.querySelectorAll('.klever-coupon__copy').forEach(function (btn) {
      btn.addEventListener('click', function () {
        var code = btn.getAttribute('data-code');
        if (!code) return;
        if (navigator.clipboard) {
          navigator.clipboard.writeText(code);
        } else {
          var tmp = document.createElement('input');
          tmp.value = code;
          document.body.appendChild(tmp);
          tmp.select();
          document.execCommand('copy');
          document.body.removeChild(tmp);
        }
        btn.textContent = 'ĐÃ SAO CHÉP!';
        btn.classList.add('copied');
        setTimeout(function () {
          btn.textContent = 'SAO CHÉP MÃ';
          btn.classList.remove('copied');
        }, 2000);
      });
    });
  }

  function initGiftTabs() {
    var tabs = document.querySelectorAll('.klever-gifts__tab');
    var panels = document.querySelectorAll('.klever-gifts__panel');
    if (!tabs.length) return;

    tabs.forEach(function (tab) {
      tab.addEventListener('click', function () {
        var id = tab.getAttribute('data-tab');
        tabs.forEach(function (t) { t.classList.remove('klever-gifts__tab--active'); });
        panels.forEach(function (p) {
          p.classList.toggle('klever-gifts__panel--active', p.getAttribute('data-panel') === id);
        });
        tab.classList.add('klever-gifts__tab--active');
      });
    });
  }

  function initStoryCarousel() {
    var track = document.getElementById('klever-story-track');
    var prev = document.getElementById('klever-story-prev');
    var next = document.getElementById('klever-story-next');
    if (!track) return;

    var scrollAmount = 272;

    if (prev) {
      prev.addEventListener('click', function () {
        track.scrollBy({ left: -scrollAmount, behavior: 'smooth' });
      });
    }
    if (next) {
      next.addEventListener('click', function () {
        track.scrollBy({ left: scrollAmount, behavior: 'smooth' });
      });
    }
  }

  function initScrollTop() {
    var btn = document.getElementById('klever-scroll-top');
    if (!btn) return;

    window.addEventListener('scroll', function () {
      btn.classList.toggle('visible', window.scrollY > 300);
    });

    btn.addEventListener('click', function () {
      window.scrollTo({ top: 0, behavior: 'smooth' });
    });
  }

  document.addEventListener('DOMContentLoaded', function () {
    initCountdown();
    initDrawer();
    initHeroSlider();
    initCouponCopy();
    initGiftTabs();
    initStoryCarousel();
    initScrollTop();
  });
})();
