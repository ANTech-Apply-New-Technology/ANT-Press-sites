/**
 * Contact Protection — Anti-bot obfuscation + click-to-copy
 *
 * Contact info is NOT in the HTML source. This JS decodes and renders it
 * at runtime, stopping email/phone scrapers. Phones call on mobile,
 * copy on desktop. Email always copies.
 */
(function () {
	/* Encoded as char-code arrays — invisible to regex scrapers */
	var C = {
		p1: [43,52,54,32,55,48,56,32,51,53,54,32,55,51,48],
		p2: [43,52,54,32,55,51,32,53,54,49,32,57,53,32,55,51],
		em: [105,110,102,111,64,115,109,117,103,103,108,101,114,98,111,97,116,115,46,115,101]
	};

	function decode(arr) {
		for (var s = '', i = 0; i < arr.length; i++) s += String.fromCharCode(arr[i]);
		return s;
	}

	function strip(s) { return s.replace(/\s/g, ''); }

	var isMobile = /Mobi|Android/i.test(navigator.userAgent);

	document.querySelectorAll('[data-cp]').forEach(function (el) {
		var key = el.getAttribute('data-cp');
		if (!C[key]) return;

		var val = decode(C[key]);
		var raw = key === 'em' ? val : strip(val);

		var a = document.createElement('a');
		a.textContent = val;
		a.className = 'cp-link';

		if (key === 'em') {
			/* Email: always copy */
			a.href = '#';
			a.setAttribute('role', 'button');
			a.title = 'Klicka for att kopiera';
			a.addEventListener('click', function (e) {
				e.preventDefault();
				copyAndToast(raw, a);
			});
		} else {
			/* Phone: call on mobile, copy on desktop */
			if (isMobile) {
				a.href = 'tel:' + raw;
			} else {
				a.href = '#';
				a.setAttribute('role', 'button');
				a.title = 'Klicka for att kopiera';
				a.addEventListener('click', function (e) {
					e.preventDefault();
					copyAndToast(val, a);
				});
			}
		}

		el.textContent = '';
		el.appendChild(a);
	});

	function copyAndToast(text, anchor) {
		if (navigator.clipboard && navigator.clipboard.writeText) {
			navigator.clipboard.writeText(text).then(function () { toast(anchor); });
		} else {
			/* Fallback for older browsers */
			var ta = document.createElement('textarea');
			ta.value = text;
			ta.style.position = 'fixed';
			ta.style.opacity = '0';
			document.body.appendChild(ta);
			ta.select();
			document.execCommand('copy');
			document.body.removeChild(ta);
			toast(anchor);
		}
	}

	function toast(anchor) {
		var t = document.createElement('span');
		t.className = 'cp-toast';
		t.textContent = 'Kopierad!';
		anchor.parentNode.appendChild(t);
		setTimeout(function () { t.classList.add('cp-toast-show'); }, 10);
		setTimeout(function () {
			t.classList.remove('cp-toast-show');
			setTimeout(function () { t.remove(); }, 200);
		}, 1400);
	}
})();
