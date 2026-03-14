<?php
/**
 * Smuggler Theme functions and definitions
 *
 * @package Smuggler_Theme
 * @since 1.0.0
 */

if ( ! function_exists( 'smuggler_theme_support' ) ) :
	function smuggler_theme_support() {
		add_editor_style( 'style.css' );
		load_theme_textdomain( 'smuggler-theme' );

		// Register nav menu locations for the overlay menu
		register_nav_menus( array(
			'overlay-col-1' => __( 'Overlay Menu — Column 1', 'smuggler-theme' ),
			'overlay-col-2' => __( 'Overlay Menu — Column 2', 'smuggler-theme' ),
			'overlay-col-3' => __( 'Overlay Menu — Column 3', 'smuggler-theme' ),
			'overlay-mobile' => __( 'Overlay Menu — Mobile', 'smuggler-theme' ),
		) );
	}
endif;
add_action( 'after_setup_theme', 'smuggler_theme_support' );

/**
 * Customizer: Company Info fields.
 * Allows end-customer to update business info from Appearance > Customize.
 */
function smuggler_customizer_register( $wp_customize ) {
	// Section: Company Info
	$wp_customize->add_section( 'smuggler_company_info', array(
		'title'    => __( 'Företagsinformation', 'smuggler-theme' ),
		'priority' => 30,
	) );

	$fields = array(
		'company_name'    => array( 'label' => 'Företagsnamn', 'default' => 'Smugglerbåtar AB' ),
		'company_address' => array( 'label' => 'Adress', 'default' => 'Kråkviksv. 8, 761 94 Norrtälje' ),
		'company_phone'   => array( 'label' => 'Telefon', 'default' => '' ),
		'company_email'   => array( 'label' => 'E-post', 'default' => '' ),
		'company_org_nr'  => array( 'label' => 'Org.nr', 'default' => '' ),
		'company_founded' => array( 'label' => 'Grundat år', 'default' => '2001' ),
	);

	foreach ( $fields as $key => $args ) {
		$wp_customize->add_setting( "smuggler_$key", array(
			'default'           => $args['default'],
			'sanitize_callback' => 'sanitize_text_field',
		) );
		$wp_customize->add_control( "smuggler_$key", array(
			'label'   => $args['label'],
			'section' => 'smuggler_company_info',
			'type'    => 'text',
		) );
	}

	// Section: Social Links
	$wp_customize->add_section( 'smuggler_social', array(
		'title'    => __( 'Sociala medier', 'smuggler-theme' ),
		'priority' => 31,
	) );

	$socials = array(
		'social_facebook'  => 'Facebook URL',
		'social_instagram' => 'Instagram URL',
		'social_youtube'   => 'YouTube URL',
	);

	foreach ( $socials as $key => $label ) {
		$wp_customize->add_setting( "smuggler_$key", array(
			'default'           => '',
			'sanitize_callback' => 'esc_url_raw',
		) );
		$wp_customize->add_control( "smuggler_$key", array(
			'label'   => $label,
			'section' => 'smuggler_social',
			'type'    => 'url',
		) );
	}
}
add_action( 'customize_register', 'smuggler_customizer_register' );

/**
 * Helper: get company info from Customizer (with fallback).
 */
function smuggler_company( $field, $fallback = '' ) {
	return get_theme_mod( "smuggler_$field", $fallback );
}

/**
 * Disable wptexturize — prevents WordPress from converting straight quotes
 * to curly quotes and other "smart" character replacements that break
 * Swedish text display.
 */
add_filter( 'run_wptexturize', '__return_false' );

if ( ! function_exists( 'smuggler_theme_styles' ) ) :
	function smuggler_theme_styles() {
		wp_register_style(
			'smuggler-theme-style',
			get_stylesheet_directory_uri() . '/style.css',
			array(),
			wp_get_theme()->get( 'Version' )
		);
		wp_enqueue_style( 'smuggler-theme-style' );

		wp_register_style(
			'smuggler-theme-custom',
			get_stylesheet_directory_uri() . '/assets/css/custom.css',
			array( 'smuggler-theme-style' ),
			wp_get_theme()->get( 'Version' )
		);
		wp_enqueue_style( 'smuggler-theme-custom' );
	}
endif;
add_action( 'wp_enqueue_scripts', 'smuggler_theme_styles' );

if ( ! function_exists( 'smuggler_theme_scripts' ) ) :
	function smuggler_theme_scripts() {
		wp_enqueue_script(
			'smuggler-header-scroll',
			get_stylesheet_directory_uri() . '/assets/js/header-scroll.js',
			array(),
			wp_get_theme()->get( 'Version' ),
			true
		);

		wp_enqueue_script(
			'smuggler-scroll-animations',
			get_stylesheet_directory_uri() . '/assets/js/scroll-animations.js',
			array(),
			wp_get_theme()->get( 'Version' ),
			true
		);

		wp_enqueue_script(
			'smuggler-contact-protect',
			get_stylesheet_directory_uri() . '/assets/js/contact-protect.js',
			array(),
			wp_get_theme()->get( 'Version' ),
			true
		);

		if ( is_page( 'tjänster' ) ) {
			wp_enqueue_script(
				'smuggler-price-calculator',
				get_stylesheet_directory_uri() . '/assets/js/price-calculator.js',
				array(),
				wp_get_theme()->get( 'Version' ),
				true
			);
		}
	}
endif;
add_action( 'wp_enqueue_scripts', 'smuggler_theme_scripts' );

/**
 * Register block pattern categories.
 */
if ( ! function_exists( 'smuggler_theme_pattern_categories' ) ) :
	function smuggler_theme_pattern_categories() {
		register_block_pattern_category( 'smuggler-hero', array(
			'label' => __( 'Smuggler Hero', 'smuggler-theme' ),
		) );
		register_block_pattern_category( 'smuggler-cards', array(
			'label' => __( 'Smuggler Cards', 'smuggler-theme' ),
		) );
		register_block_pattern_category( 'smuggler-sections', array(
			'label' => __( 'Smuggler Sections', 'smuggler-theme' ),
		) );
	}
endif;
add_action( 'init', 'smuggler_theme_pattern_categories' );

/**
 * SVERA Calendar Shortcode — [svera_kalender]
 *
 * Fetches the race calendar from svera.nu, filters offshore events,
 * and renders a styled table. Cached for 24 hours via transients.
 */
function smuggler_svera_kalender_shortcode( $atts ) {
	$atts = shortcode_atts( array(
		'branch' => 'offshore',
	), $atts, 'svera_kalender' );

	$cache_key = 'svera_kalender_' . $atts['branch'];
	$cached    = get_transient( $cache_key );
	if ( false !== $cached ) {
		return $cached;
	}

	$response = wp_remote_get( 'https://www.svera.nu/kalender.html', array(
		'timeout' => 10,
	) );

	if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {
		return '<p style="color:#a0b4cc;text-align:center;">Kunde inte ladda kalendern från svera.nu. <a href="https://www.svera.nu/kalender.html" style="color:#E8601C">Visa på svera.nu</a></p>';
	}

	$html = wp_remote_retrieve_body( $response );

	libxml_use_internal_errors( true );
	$doc = new DOMDocument();
	$doc->loadHTML( '<?xml encoding="UTF-8">' . $html );
	libxml_clear_errors();

	$xpath = new DOMXPath( $doc );
	$rows  = $xpath->query( '//tbody[@id="svemo-events"]/tr[@data-branch="' . esc_attr( $atts['branch'] ) . '"]' );

	if ( ! $rows || 0 === $rows->length ) {
		return '<p style="color:#a0b4cc;text-align:center;">Inga tävlingar hittades.</p>';
	}

	$output  = '<table class="svera-kalender-table">';
	$output .= '<thead><tr><th>Datum</th><th>Tävling</th><th>Arrangör</th><th class="svera-hide-mobile">Klasser</th></tr></thead>';
	$output .= '<tbody>';

	$months_sv = array(
		'01' => 'jan', '02' => 'feb', '03' => 'mar', '04' => 'apr',
		'05' => 'maj', '06' => 'jun', '07' => 'jul', '08' => 'aug',
		'09' => 'sep', '10' => 'okt', '11' => 'nov', '12' => 'dec',
	);

	foreach ( $rows as $row ) {
		$cells = $row->getElementsByTagName( 'td' );
		if ( $cells->length < 5 ) {
			continue;
		}

		$raw_date  = trim( $cells->item( 0 )->textContent );
		$event     = trim( $cells->item( 1 )->textContent );
		$organizer = trim( $cells->item( 3 )->textContent );
		$classes   = trim( $cells->item( 4 )->textContent );

		// Format date: 2026-06-13 → 13 jun
		if ( preg_match( '/^\d{4}-(\d{2})-(\d{2})$/', $raw_date, $m ) ) {
			$display_date = intval( $m[2] ) . ' ' . $months_sv[ $m[1] ];
		} else {
			$display_date = esc_html( $raw_date );
		}

		$output .= '<tr>';
		$output .= '<td>' . esc_html( $display_date ) . '</td>';
		$output .= '<td>' . esc_html( $event ) . '</td>';
		$output .= '<td>' . esc_html( $organizer ) . '</td>';
		$output .= '<td class="svera-hide-mobile">' . esc_html( $classes ) . '</td>';
		$output .= '</tr>';
	}

	$output .= '</tbody></table>';
	$output .= '<p class="svera-kalender-source">Källa: <a href="https://www.svera.nu/kalender.html">svera.nu</a> — uppdateras automatiskt</p>';

	set_transient( $cache_key, $output, DAY_IN_SECONDS );

	return $output;
}
add_shortcode( 'svera_kalender', 'smuggler_svera_kalender_shortcode' );

/**
 * Nimbus-style fullscreen menu overlay.
 * Outputs the overlay HTML in wp_footer so it's outside the block template.
 */
/**
 * Default menu fallback — used when no WordPress menu is assigned.
 * This provides the initial hardcoded links so the theme works out of the box.
 * Once the customer creates menus in Appearance > Menus, these are replaced.
 */
function smuggler_fallback_menu_col1() {
	echo '<ul><li><a href="/batar/">Smuggler 21</a></li><li><a href="/batar/">Smuggler 24</a></li><li><a href="/batar/">Smuggler 28</a></li><li><a href="/batar/">Smuggler RS</a></li></ul>';
}
function smuggler_fallback_menu_col2() {
	echo '<ul><li><a href="/om-oss/">Om oss</a></li><li><a href="/tjänster/">Tjänster</a></li><li><a href="/produkter/">Produkter</a></li><li><a href="/racing/">Racing</a></li></ul>';
}
function smuggler_fallback_menu_col3() {
	echo '<ul><li><a href="/kontakt/">Kontakta oss</a></li><li><a href="tel:+46000000000">Telefon</a></li></ul>';
}

function smuggler_menu_overlay() {
	$address = smuggler_company( 'company_address', 'Kråkviksv. 8, 761 94 Norrtälje' );
	$address_lines = str_replace( ', ', '<br>', esc_html( $address ) );
	?>
	<div class="smuggler-menu-overlay" aria-hidden="true">
		<div class="smuggler-menu-overlay-inner">

			<!-- Desktop: multi-column layout (editable via Appearance > Menus) -->
			<div class="smuggler-menu-columns">
				<div class="smuggler-menu-col">
					<h3><?php echo esc_html( wp_get_nav_menu_name( 'overlay-col-1' ) ?: 'Båtar' ); ?></h3>
					<?php wp_nav_menu( array(
						'theme_location'  => 'overlay-col-1',
						'container'       => false,
						'fallback_cb'     => 'smuggler_fallback_menu_col1',
						'depth'           => 1,
					) ); ?>
				</div>
				<div class="smuggler-menu-col">
					<h3><?php echo esc_html( wp_get_nav_menu_name( 'overlay-col-2' ) ?: 'Företaget' ); ?></h3>
					<?php wp_nav_menu( array(
						'theme_location'  => 'overlay-col-2',
						'container'       => false,
						'fallback_cb'     => 'smuggler_fallback_menu_col2',
						'depth'           => 1,
					) ); ?>
				</div>
				<div class="smuggler-menu-col">
					<h3><?php echo esc_html( wp_get_nav_menu_name( 'overlay-col-3' ) ?: 'Kontakt' ); ?></h3>
					<?php wp_nav_menu( array(
						'theme_location'  => 'overlay-col-3',
						'container'       => false,
						'fallback_cb'     => 'smuggler_fallback_menu_col3',
						'depth'           => 1,
					) ); ?>
					<div class="smuggler-menu-address">
						<p><?php echo $address_lines; ?></p>
					</div>
				</div>
			</div>

			<!-- Mobile: accordion layout (mirrors desktop menus) -->
			<div class="smuggler-menu-mobile">
				<?php
				$mobile_location = has_nav_menu( 'overlay-mobile' ) ? 'overlay-mobile' : null;
				$cols = array(
					array( 'location' => 'overlay-col-1', 'label' => wp_get_nav_menu_name( 'overlay-col-1' ) ?: 'Båtar', 'fallback' => 'smuggler_fallback_menu_col1' ),
					array( 'location' => 'overlay-col-2', 'label' => wp_get_nav_menu_name( 'overlay-col-2' ) ?: 'Företaget', 'fallback' => 'smuggler_fallback_menu_col2' ),
				);
				foreach ( $cols as $col ) : ?>
					<div class="smuggler-menu-accordion">
						<button class="smuggler-menu-accordion-header" aria-expanded="false">
							<?php echo esc_html( $col['label'] ); ?> <span class="accordion-arrow" aria-hidden="true"></span>
						</button>
						<div class="smuggler-menu-accordion-body">
							<?php
							// Render as flat links (no <ul>) for mobile
							$items = wp_get_nav_menu_items( wp_get_nav_menu_name( $col['location'] ) );
							if ( $items ) {
								foreach ( $items as $item ) {
									echo '<a href="' . esc_url( $item->url ) . '">' . esc_html( $item->title ) . '</a>';
								}
							} else {
								// Fallback: use the same hardcoded links
								$col['fallback']();
							}
							?>
						</div>
					</div>
				<?php endforeach; ?>
				<a href="/kontakt/" class="smuggler-menu-mobile-link">Kontakt</a>
			</div>

			<!-- Bottom CTA row (desktop) -->
			<div class="smuggler-menu-bottom">
				<a href="/kontakt/" class="smuggler-menu-bottom-link">Kontakta oss</a>
			</div>

		</div>
	</div>
	<?php
}
add_action( 'wp_footer', 'smuggler_menu_overlay' );

/**
 * Basic SEO: meta descriptions and Open Graph tags.
 */
function smuggler_seo_meta_tags() {
	$description = get_bloginfo( 'description' );
	$title       = wp_get_document_title();
	$url         = home_url( $_SERVER['REQUEST_URI'] );
	$site_name   = get_bloginfo( 'name' );

	if ( is_singular() ) {
		$post = get_queried_object();
		if ( $post && ! empty( $post->post_excerpt ) ) {
			$description = wp_strip_all_tags( $post->post_excerpt );
		} elseif ( $post && ! empty( $post->post_content ) ) {
			$description = wp_trim_words( wp_strip_all_tags( $post->post_content ), 25, '...' );
		}
	}

	$description = esc_attr( $description );

	echo '<meta name="description" content="' . $description . '">' . "\n";
	echo '<meta property="og:title" content="' . esc_attr( $title ) . '">' . "\n";
	echo '<meta property="og:description" content="' . $description . '">' . "\n";
	echo '<meta property="og:url" content="' . esc_url( $url ) . '">' . "\n";
	echo '<meta property="og:site_name" content="' . esc_attr( $site_name ) . '">' . "\n";
	echo '<meta property="og:type" content="website">' . "\n";
	echo '<meta property="og:locale" content="sv_SE">' . "\n";
}
add_action( 'wp_head', 'smuggler_seo_meta_tags', 1 );
