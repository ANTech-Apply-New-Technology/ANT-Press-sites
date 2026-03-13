/**
 * Makiro Theme — Main JS
 */

(function() {
  'use strict';

  // Header scroll effect
  const header = document.getElementById('siteHeader');
  if (header) {
    let ticking = false;
    window.addEventListener('scroll', function() {
      if (!ticking) {
        requestAnimationFrame(function() {
          header.classList.toggle('scrolled', window.scrollY > 50);
          ticking = false;
        });
        ticking = true;
      }
    });
  }

  // Scroll-triggered animations
  const animateElements = document.querySelectorAll('.animate-in');
  if (animateElements.length && 'IntersectionObserver' in window) {
    const observer = new IntersectionObserver(function(entries) {
      entries.forEach(function(entry) {
        if (entry.isIntersecting) {
          entry.target.classList.add('visible');
          observer.unobserve(entry.target);
        }
      });
    }, {
      threshold: 0.1,
      rootMargin: '0px 0px -50px 0px'
    });

    animateElements.forEach(function(el, i) {
      el.style.transitionDelay = (i % 4) * 100 + 'ms';
      observer.observe(el);
    });
  }

  // Smooth scroll for anchor links
  document.querySelectorAll('a[href^="#"]').forEach(function(link) {
    link.addEventListener('click', function(e) {
      var targetId = this.getAttribute('href');
      if (targetId === '#') return;
      var target = document.querySelector(targetId);
      if (target) {
        e.preventDefault();
        var offset = 80; // header height
        var top = target.getBoundingClientRect().top + window.pageYOffset - offset;
        window.scrollTo({ top: top, behavior: 'smooth' });
      }
    });
  });

  // Product tabs (filter by category)
  document.querySelectorAll('.product-tab').forEach(function(tab) {
    tab.addEventListener('click', function() {
      document.querySelectorAll('.product-tab').forEach(function(t) {
        t.classList.remove('active');
      });
      this.classList.add('active');

      var filter = this.textContent.trim();
      document.querySelectorAll('.product-card').forEach(function(card) {
        if (filter === 'Alla') {
          card.style.display = '';
        } else {
          var cat = card.querySelector('.product-category-label');
          var matches = cat && cat.textContent.trim().toLowerCase().indexOf(filter.toLowerCase()) !== -1;
          card.style.display = matches ? '' : 'none';
        }
      });
    });
  });

})();
