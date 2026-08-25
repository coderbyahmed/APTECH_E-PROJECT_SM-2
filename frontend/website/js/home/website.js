/* SOUND Group - Website JavaScript */
(function () {
    'use strict';

    /* Mobile Menu Toggle */
    var toggle = document.getElementById('wgMenuToggle');
    var menu = document.getElementById('wgMobileMenu');
    if (toggle && menu) {
        toggle.addEventListener('click', function () {
            toggle.classList.toggle('is-active');
            menu.classList.toggle('is-open');
        });
        var mobileLinks = menu.querySelectorAll('.wg-mobile-nav__link, .wg-btn');
        for (var i = 0; i < mobileLinks.length; i++) {
            mobileLinks[i].addEventListener('click', function () {
                toggle.classList.remove('is-active');
                menu.classList.remove('is-open');
            });
        }
    }

    /* Header scroll effect */
    var header = document.getElementById('wgHeader');
    if (header) {
        window.addEventListener('scroll', function () {
            if (window.scrollY > 20) {
                header.classList.add('is-scrolled');
            } else {
                header.classList.remove('is-scrolled');
            }
        });
    }

    /* Active nav link on scroll */
    var sections = document.querySelectorAll('.wg-hero, .wg-section');
    var navLinks = document.querySelectorAll('.wg-nav__link');
    function setActiveNav() {
        var scrollY = window.scrollY + 100;
        sections.forEach(function (section) {
            var top = section.offsetTop;
            var height = section.offsetHeight;
            var id = section.getAttribute('id');
            if (scrollY >= top && scrollY < top + height) {
                navLinks.forEach(function (link) {
                    link.classList.remove('wg-nav__link--active');
                    if (link.getAttribute('href') === '#' + id) {
                        link.classList.add('wg-nav__link--active');
                    }
                });
            }
        });
    }
    window.addEventListener('scroll', setActiveNav);

    /* Smooth scroll for anchor links */
    var anchorLinks = document.querySelectorAll('a[href^="#"]');
    anchorLinks.forEach(function (link) {
        link.addEventListener('click', function (e) {
            var target = document.querySelector(this.getAttribute('href'));
            if (target) {
                e.preventDefault();
                target.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        });
    });

    /* Card play button hover feedback */
    var musicCards = document.querySelectorAll('.wg-card--music');
    musicCards.forEach(function (card) {
        var playBtn = card.querySelector('.wg-card__play');
        if (playBtn) {
            playBtn.addEventListener('click', function () {
                playBtn.style.opacity = '0';
                setTimeout(function () { playBtn.style.opacity = ''; }, 300);
            });
        }
    });
})();
