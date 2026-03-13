<?php
/**
 * Title: Price Calculator
 * Slug: smuggler-theme/price-calculator
 * Categories: smuggler-sections
 */
?>

<!-- wp:group {"align":"full","backgroundColor":"surface","style":{"spacing":{"padding":{"top":"var:preset|spacing|80","bottom":"var:preset|spacing|80","left":"var:preset|spacing|50","right":"var:preset|spacing|50"}}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group alignfull has-surface-background-color has-background" style="padding-top:var(--wp--preset--spacing--80);padding-right:var(--wp--preset--spacing--50);padding-bottom:var(--wp--preset--spacing--80);padding-left:var(--wp--preset--spacing--50)">

	<!-- wp:heading {"textAlign":"center","style":{"spacing":{"margin":{"bottom":"var:preset|spacing|60"}}}} -->
	<h2 class="wp-block-heading has-text-align-center" style="margin-bottom:var(--wp--preset--spacing--60)">Priskalkylator Förvaring</h2>
	<!-- /wp:heading -->

	<!-- wp:html -->
	<div class="smuggler-calculator">

		<div class="calc-inputs">
			<div class="calc-row">
				<label for="calc-length">Båtens längd:</label>
				<input type="number" id="calc-length" min="0" step="0.1" value="1" />
				<select id="calc-length-unit">
					<option value="meter">meter</option>
					<option value="foot">fot</option>
				</select>
			</div>
			<div class="calc-row">
				<label for="calc-width">Båtens bredd:</label>
				<input type="number" id="calc-width" min="0" step="0.1" value="1" />
				<select id="calc-width-unit">
					<option value="meter">meter</option>
					<option value="foot">fot</option>
				</select>
			</div>
			<div class="calc-area-display">
				Automatiskt beräknad yta (inkl. 1 kvm): <strong><span id="calc-area">2.0</span> kvm</strong>
			</div>
		</div>

		<div class="calc-section">
			<h3>Välj tjänster:</h3>
			<div class="calc-checkboxes">
				<label><input type="checkbox" class="calc-service" data-price="570" data-per-kvm /> Vinterförvaring i vårt kalla tält <span class="calc-price-hint">570 kr/kvm</span></label>
				<label><input type="checkbox" class="calc-service" data-price="295" data-per-kvm /> Vinterförvaring utomhus <span class="calc-price-hint">295 kr/kvm</span></label>
				<label><input type="checkbox" class="calc-service" data-price="245" data-per-kvm /> Inplastning <span class="calc-price-hint">245 kr/kvm</span></label>
				<label><input type="checkbox" class="calc-service" data-price-under="1850" data-price-over="2300" data-threshold="6" /> Upptagning med trailer <span class="calc-price-hint">1 850 / 2 300 kr</span></label>
				<label><input type="checkbox" class="calc-service" data-price-under="1850" data-price-over="2300" data-threshold="6" /> Sjösättning med trailer <span class="calc-price-hint">1 850 / 2 300 kr</span></label>
				<label><input type="checkbox" class="calc-service" data-price="300" data-per-length /> Bottentvätt högtryck <span class="calc-price-hint">300 kr/m</span></label>
				<label><input type="checkbox" class="calc-service" data-price="2400" /> Konservering motor <span class="calc-price-hint">2 400 kr</span></label>
				<label><input type="checkbox" class="calc-service" data-price="1250" /> Drevservice <span class="calc-price-hint">1 250 kr</span></label>
				<label><input type="checkbox" class="calc-service" data-price="3250" /> Motorservice inombordare bensin <span class="calc-price-hint">3 250 kr</span></label>
				<label><input type="checkbox" class="calc-service" data-price="4200" /> Motorservice diesel <span class="calc-price-hint">4 200 kr</span></label>
				<label><input type="checkbox" class="calc-service" data-price="2050" /> Service utombordare 4-takt (t.o.m. 100 Hk) <span class="calc-price-hint">2 050 kr</span></label>
				<label><input type="checkbox" class="calc-service" data-price="2450" /> Service utombordare 4-takt (fr.o.m. 101 Hk) <span class="calc-price-hint">2 450 kr</span></label>
				<label><input type="checkbox" class="calc-service" data-price="1500" /> Service utombordare 2-takt (t.o.m. 100 Hk) <span class="calc-price-hint">1 500 kr</span></label>
				<label><input type="checkbox" class="calc-service" data-price="1900" /> Service utombordare 2-takt (fr.o.m. 101 Hk) <span class="calc-price-hint">1 900 kr</span></label>
			</div>
		</div>

		<div class="calc-section">
			<h3>Material/Fasta kostnader:</h3>
			<div class="calc-checkboxes">
				<label><input type="checkbox" class="calc-material" data-price="300" /> Motorolja 25w-50 <span class="calc-price-hint">300 kr</span></label>
				<label><input type="checkbox" class="calc-material" data-price="315" /> Drev olja <span class="calc-price-hint">315 kr</span></label>
				<label><input type="checkbox" class="calc-material" data-price="537" /> Service kit <span class="calc-price-hint">537 kr</span></label>
				<label><input type="checkbox" class="calc-material" data-price="252" /> Bränslefilter <span class="calc-price-hint">252 kr</span></label>
				<label><input type="checkbox" class="calc-material" data-price="45" /> Oljepluggs packningar <span class="calc-price-hint">45 kr</span></label>
			</div>
		</div>

		<div class="calc-total">
			<span class="calc-total-label">Totalpris:</span>
			<span class="calc-total-price"><span id="calc-price">0</span> kr</span>
		</div>

		<p class="calc-disclaimer">Glöm inte att detta är estimeringspriser. Priser varierar på motorer och båtar.</p>

	</div>
	<!-- /wp:html -->

</div>
<!-- /wp:group -->
