<?php
/**
 * Title: About Split
 * Slug: smuggler-theme/about-split
 * Categories: smuggler-sections
 */
?>

<!-- wp:group {"align":"full","backgroundColor":"background","style":{"spacing":{"padding":{"top":"0","bottom":"0","left":"0","right":"0"}}},"layout":{"type":"default"}} -->
<div class="wp-block-group alignfull has-background-background-color has-background" style="padding-top:0;padding-right:0;padding-bottom:0;padding-left:0">

	<!-- wp:columns {"align":"full","className":"nimbus-alt-row","style":{"spacing":{"blockGap":{"left":"0"}}}} -->
	<div class="wp-block-columns alignfull nimbus-alt-row">

		<!-- wp:column {"width":"50%"} -->
		<div class="wp-block-column" style="flex-basis:50%">
			<!-- wp:image {"sizeSlug":"full","style":{"border":{"radius":"0px"}}} -->
			<figure class="wp-block-image size-full" style="border-radius:0px"><img src="<?php echo esc_url( get_stylesheet_directory_uri() ); ?>/assets/images/workshop-promo.jpg" alt="Smugglerbåtar AB verkstad"/></figure>
			<!-- /wp:image -->
		</div>
		<!-- /wp:column -->

		<!-- wp:column {"width":"50%","verticalAlignment":"center","style":{"spacing":{"padding":{"top":"var:preset|spacing|70","bottom":"var:preset|spacing|70","left":"var:preset|spacing|70","right":"var:preset|spacing|70"}}}} -->
		<div class="wp-block-column is-vertically-aligned-center" style="flex-basis:50%;padding-top:var(--wp--preset--spacing--70);padding-right:var(--wp--preset--spacing--70);padding-bottom:var(--wp--preset--spacing--70);padding-left:var(--wp--preset--spacing--70)">

			<!-- wp:heading {"style":{"spacing":{"margin":{"bottom":"var:preset|spacing|40"}}}} -->
			<h2 class="wp-block-heading" style="margin-bottom:var(--wp--preset--spacing--40)">Vår Historia</h2>
			<!-- /wp:heading -->

			<!-- wp:paragraph {"textColor":"muted","style":{"typography":{"lineHeight":"1.9","fontSize":"0.95rem"}}} -->
			<p class="has-muted-color has-text-color" style="font-size:0.95rem;line-height:1.9">Smugglerbåtar AB startade verksamheten under 2001 genom att förvärva de gamla formarna. Vi bygger klassiska Smuggler-båtar från originalformarna — handbyggda och skräddarsydda efter dina önskemål.</p>
			<!-- /wp:paragraph -->

			<!-- wp:buttons {"style":{"spacing":{"margin":{"top":"var:preset|spacing|40"}}}} -->
			<div class="wp-block-buttons" style="margin-top:var(--wp--preset--spacing--40)">
				<!-- wp:button {"className":"is-style-outline"} -->
				<div class="wp-block-button is-style-outline"><a class="wp-block-button__link wp-element-button" href="/om-oss/">Läs mer</a></div>
				<!-- /wp:button -->
			</div>
			<!-- /wp:buttons -->

		</div>
		<!-- /wp:column -->

	</div>
	<!-- /wp:columns -->

</div>
<!-- /wp:group -->
