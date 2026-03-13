/**
 * Smuggler Theme — Header Scroll + Menu Overlay (Nimbus-style)
 *
 * 1. Toggles .is-transparent / .is-solid on scroll
 * 2. Handles fullscreen menu overlay toggle
 */
(function () {
	'use strict';

	/* =======================================================================
	   HEADER SCROLL
	   ======================================================================= */
	var header = document.querySelector('.smuggler-header');
	if (!header) return;

	var scrollThreshold = 80;

	// Detect dark hero for transparent header
	var firstContent = header.parentElement ? header.parentElement.nextElementSibling : null;
	if (!firstContent) {
		// Block theme: header is inside wp-block-template-part
		var templatePart = document.querySelector('.wp-site-blocks > .wp-block-template-part');
		firstContent = templatePart ? templatePart.nextElementSibling : null;
	}

	var hasDarkHero = !!(
		document.querySelector('.smuggler-hero') ||
		(firstContent && firstContent.querySelector('.wp-block-cover')) ||
		(firstContent && firstContent.classList.contains('wp-block-cover')) ||
		(firstContent && (
			firstContent.classList.contains('has-primary-background-color') ||
			firstContent.classList.contains('has-racing-dark-background-color')
		))
	);

	var allowTransparent = hasDarkHero;
	var isTransparent = allowTransparent;

	if (!allowTransparent) {
		header.classList.remove('is-transparent');
		header.classList.add('is-solid');
	}

	function updateHeader() {
		if (!allowTransparent) return;
		var scrolled = window.scrollY > scrollThreshold;
		if (scrolled && isTransparent) {
			header.classList.remove('is-transparent');
			header.classList.add('is-solid');
			isTransparent = false;
		} else if (!scrolled && !isTransparent) {
			header.classList.remove('is-solid');
			header.classList.add('is-transparent');
			isTransparent = true;
		}
	}

	updateHeader();

	var ticking = false;
	window.addEventListener('scroll', function () {
		if (!ticking) {
			window.requestAnimationFrame(function () {
				updateHeader();
				ticking = false;
			});
			ticking = true;
		}
	});

	/* =======================================================================
	   FULLSCREEN MENU OVERLAY (Nimbus-style)
	   ======================================================================= */
	var menuToggle = document.querySelector('.smuggler-menu-toggle');
	var menuOverlay = document.querySelector('.smuggler-menu-overlay');
	if (!menuToggle || !menuOverlay) return;

	var menuOpen = false;

	function openMenu() {
		menuOpen = true;
		menuOverlay.classList.add('is-open');
		menuToggle.setAttribute('aria-expanded', 'true');
		document.body.classList.add('menu-is-open');
		// Force header to look solid/light on top of overlay
		header.classList.add('is-menu-open');
	}

	function closeMenu() {
		menuOpen = false;
		menuOverlay.classList.remove('is-open');
		menuToggle.setAttribute('aria-expanded', 'false');
		document.body.classList.remove('menu-is-open');
		header.classList.remove('is-menu-open');
	}

	menuToggle.addEventListener('click', function () {
		if (menuOpen) {
			closeMenu();
		} else {
			openMenu();
		}
	});

	// Close on Escape
	document.addEventListener('keydown', function (e) {
		if (e.key === 'Escape' && menuOpen) {
			closeMenu();
		}
	});

	// Close when clicking a menu link
	var menuLinks = menuOverlay.querySelectorAll('a');
	for (var i = 0; i < menuLinks.length; i++) {
		menuLinks[i].addEventListener('click', function () {
			closeMenu();
		});
	}

	/* Mobile accordion toggle */
	var accordionHeaders = menuOverlay.querySelectorAll('.smuggler-menu-accordion-header');
	for (var j = 0; j < accordionHeaders.length; j++) {
		accordionHeaders[j].addEventListener('click', function () {
			var parent = this.parentElement;
			var isExpanded = parent.classList.contains('is-expanded');
			// Close all
			var allItems = menuOverlay.querySelectorAll('.smuggler-menu-accordion');
			for (var k = 0; k < allItems.length; k++) {
				allItems[k].classList.remove('is-expanded');
			}
			// Toggle current
			if (!isExpanded) {
				parent.classList.add('is-expanded');
			}
		});
	}
})();
