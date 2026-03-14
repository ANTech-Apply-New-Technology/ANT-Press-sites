<?php
/**
 * Title: Contact Section
 * Slug: smuggler-theme/contact-section
 * Categories: smuggler-sections
 */
?>

<!-- wp:group {"align":"full","backgroundColor":"background","style":{"spacing":{"padding":{"top":"var:preset|spacing|80","bottom":"var:preset|spacing|80","left":"var:preset|spacing|50","right":"var:preset|spacing|50"}}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group alignfull has-background-background-color has-background" style="padding-top:var(--wp--preset--spacing--80);padding-right:var(--wp--preset--spacing--50);padding-bottom:var(--wp--preset--spacing--80);padding-left:var(--wp--preset--spacing--50)">

	<!-- wp:heading {"textAlign":"center","style":{"spacing":{"margin":{"bottom":"var:preset|spacing|60"}}}} -->
	<h2 class="wp-block-heading has-text-align-center" style="margin-bottom:var(--wp--preset--spacing--60)">Kontakta Oss</h2>
	<!-- /wp:heading -->

	<!-- wp:columns {"align":"wide","style":{"spacing":{"blockGap":{"left":"var:preset|spacing|70"}}}} -->
	<div class="wp-block-columns alignwide">

		<!-- wp:column {"width":"55%"} -->
		<div class="wp-block-column" style="flex-basis:55%">

			<!-- wp:group {"backgroundColor":"surface","style":{"spacing":{"padding":{"top":"var:preset|spacing|50","bottom":"var:preset|spacing|50","left":"var:preset|spacing|50","right":"var:preset|spacing|50"}},"border":{"radius":"4px"}},"layout":{"type":"constrained"}} -->
			<div class="wp-block-group has-surface-background-color has-background" style="border-radius:4px;padding-top:var(--wp--preset--spacing--50);padding-right:var(--wp--preset--spacing--50);padding-bottom:var(--wp--preset--spacing--50);padding-left:var(--wp--preset--spacing--50)">

				<!-- wp:paragraph {"textColor":"muted"} -->
				<p class="has-muted-color has-text-color">Hör av dig via telefon eller mejl så återkommer vi så snart vi kan.</p>
				<!-- /wp:paragraph -->

			</div>
			<!-- /wp:group -->

		</div>
		<!-- /wp:column -->

		<!-- wp:column {"width":"45%"} -->
		<div class="wp-block-column" style="flex-basis:45%">

			<!-- wp:heading {"level":3,"style":{"typography":{"fontSize":"1.25rem"},"spacing":{"margin":{"bottom":"var:preset|spacing|30"}}}} -->
			<h3 class="wp-block-heading" style="margin-bottom:var(--wp--preset--spacing--30);font-size:1.25rem">Besöksadress</h3>
			<!-- /wp:heading -->

			<!-- wp:paragraph {"textColor":"muted"} -->
			<p class="has-muted-color has-text-color">Smugglerbåtar AB<br>Kråkviksvägen 8<br>761 94 Norrtälje</p>
			<!-- /wp:paragraph -->

			<!-- wp:heading {"level":3,"style":{"typography":{"fontSize":"1.25rem"},"spacing":{"margin":{"top":"var:preset|spacing|40","bottom":"var:preset|spacing|30"}}}} -->
			<h3 class="wp-block-heading" style="margin-top:var(--wp--preset--spacing--40);margin-bottom:var(--wp--preset--spacing--30);font-size:1.25rem">Direkt</h3>
			<!-- /wp:heading -->

			<!-- wp:html -->
			<p class="has-muted-color has-text-color"><strong>Telefon:</strong> <span data-cp="p1"></span><br><strong>Telefon 2:</strong> <span data-cp="p2"></span><br><strong>E-post:</strong> <span data-cp="em"></span></p>
			<!-- /wp:html -->

		</div>
		<!-- /wp:column -->

	</div>
	<!-- /wp:columns -->

</div>
<!-- /wp:group -->
