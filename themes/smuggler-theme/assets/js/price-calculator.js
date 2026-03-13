/**
 * Smugglerbåtar AB Price Calculator
 * Calculates estimated prices for boat storage, services, and materials.
 */
(function () {
	'use strict';

	var lengthInput = document.getElementById('calc-length');
	var lengthUnitSelect = document.getElementById('calc-length-unit');
	var widthInput = document.getElementById('calc-width');
	var widthUnitSelect = document.getElementById('calc-width-unit');
	var areaSpan = document.getElementById('calc-area');
	var priceSpan = document.getElementById('calc-price');

	if (!lengthInput || !priceSpan) return;

	function convertToMeters(value, unit) {
		return unit === 'foot' ? value * 0.3048 : value;
	}

	function calculateArea() {
		var length = convertToMeters(parseFloat(lengthInput.value) || 0, lengthUnitSelect.value);
		var width = convertToMeters(parseFloat(widthInput.value) || 0, widthUnitSelect.value);
		var area = length * width + 1;
		areaSpan.textContent = area.toFixed(1);
		return area;
	}

	function calculatePrice() {
		var area = calculateArea();
		var totalPrice = 0;
		var length = convertToMeters(parseFloat(lengthInput.value) || 0, lengthUnitSelect.value);

		document.querySelectorAll('.calc-service').forEach(function (checkbox) {
			if (checkbox.checked) {
				var priceUnder = parseFloat(checkbox.getAttribute('data-price-under'));
				var priceOver = parseFloat(checkbox.getAttribute('data-price-over'));
				var threshold = parseFloat(checkbox.getAttribute('data-threshold'));
				var price = parseFloat(checkbox.getAttribute('data-price'));

				if (priceUnder && priceOver && threshold) {
					totalPrice += length > threshold ? priceOver : priceUnder;
				} else if (checkbox.hasAttribute('data-per-kvm')) {
					totalPrice += price * area;
				} else if (checkbox.hasAttribute('data-per-length')) {
					totalPrice += price * length;
				} else {
					totalPrice += price;
				}
			}
		});

		document.querySelectorAll('.calc-material').forEach(function (checkbox) {
			if (checkbox.checked) {
				var price = parseFloat(checkbox.getAttribute('data-price'));
				totalPrice += price;
			}
		});

		priceSpan.textContent = totalPrice.toFixed(0);
	}

	lengthInput.addEventListener('input', calculatePrice);
	lengthUnitSelect.addEventListener('change', calculatePrice);
	widthInput.addEventListener('input', calculatePrice);
	widthUnitSelect.addEventListener('change', calculatePrice);
	document.querySelectorAll('.calc-service, .calc-material').forEach(function (checkbox) {
		checkbox.addEventListener('change', calculatePrice);
	});

	calculatePrice();
})();
