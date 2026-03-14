<?php
/**
 * Title: News Feed
 * Slug: smuggler-theme/news-feed
 * Categories: smuggler-sections
 */
?>

<!-- wp:group {"align":"full","className":"smuggler-news","style":{"spacing":{"padding":{"top":"var:preset|spacing|80","bottom":"var:preset|spacing|80","left":"var:preset|spacing|50","right":"var:preset|spacing|50"}}},"backgroundColor":"background","layout":{"type":"constrained","contentSize":"800px"}} -->
<div class="wp-block-group alignfull smuggler-news has-background-background-color has-background" style="padding-top:var(--wp--preset--spacing--80);padding-right:var(--wp--preset--spacing--50);padding-bottom:var(--wp--preset--spacing--80);padding-left:var(--wp--preset--spacing--50)">

	<!-- wp:heading {"textAlign":"center","style":{"spacing":{"margin":{"bottom":"var:preset|spacing|60"}}}} -->
	<h2 class="wp-block-heading has-text-align-center" style="margin-bottom:var(--wp--preset--spacing--60)">Pressmeddelanden</h2>
	<!-- /wp:heading -->

	<!-- wp:query {"queryId":1,"query":{"perPage":5,"pages":0,"offset":0,"postType":"post","order":"desc","orderBy":"date","author":"","search":"","exclude":[],"sticky":"","inherit":false},"className":"smuggler-news-list"} -->
	<div class="wp-block-query smuggler-news-list">

		<!-- wp:post-template -->

			<!-- wp:group {"className":"smuggler-news-card","style":{"spacing":{"padding":{"top":"var:preset|spacing|40","bottom":"var:preset|spacing|40"}}},"layout":{"type":"flex","justifyContent":"space-between","flexWrap":"nowrap"}} -->
			<div class="wp-block-group smuggler-news-card" style="padding-top:var(--wp--preset--spacing--40);padding-bottom:var(--wp--preset--spacing--40)">

				<!-- wp:post-date {"textColor":"muted","style":{"typography":{"fontSize":"0.8rem","fontWeight":"400"},"layout":{"selfStretch":"fixed","flexSize":"120px"}}} /-->

				<!-- wp:post-title {"level":3,"isLink":true,"style":{"typography":{"fontSize":"1rem","fontWeight":"400"},"spacing":{"margin":{"bottom":"0"}}}} /-->

			</div>
			<!-- /wp:group -->

		<!-- /wp:post-template -->

		<!-- wp:query-no-results -->
			<!-- wp:paragraph {"align":"center","textColor":"muted","style":{"typography":{"fontSize":"1rem"}}} -->
			<p class="has-text-align-center has-muted-color has-text-color" style="font-size:1rem">Inga nyheter annu — hall utkik!</p>
			<!-- /wp:paragraph -->
		<!-- /wp:query-no-results -->

	</div>
	<!-- /wp:query -->

</div>
<!-- /wp:group -->
