(function () {
    'use strict';

    // Header scroll state
    var header = document.getElementById('wgHeader');
    if (header) {
        var onScroll = function () {
            if (window.scrollY > 60) {
                header.classList.add('is-scrolled');
            } else {
                header.classList.remove('is-scrolled');
            }
        };
        window.addEventListener('scroll', onScroll, { passive: true });
        onScroll();
    }

    // Scroll-triggered animations using IntersectionObserver
    var animatedEls = document.querySelectorAll('[data-animate]');
    if (!animatedEls.length) return;

    // Respect reduced-motion preference
    if (window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
        animatedEls.forEach(function (el) { el.classList.add('is-visible'); });
        return;
    }

    if (!('IntersectionObserver' in window)) {
        animatedEls.forEach(function (el) { el.classList.add('is-visible'); });
        return;
    }

    var observer = new IntersectionObserver(function (entries) {
        entries.forEach(function (entry) {
            if (entry.isIntersecting) {
                entry.target.classList.add('is-visible');
                observer.unobserve(entry.target);
            }
        });
    }, {
        threshold: 0.15,
        rootMargin: '0px 0px -40px 0px'
    });

    animatedEls.forEach(function (el) {
        observer.observe(el);
    });
})();
