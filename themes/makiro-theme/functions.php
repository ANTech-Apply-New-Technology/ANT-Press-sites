<?php
/**
 * Makiro Theme functions
 */

function makiro_setup() {
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('html5', ['search-form', 'comment-form', 'comment-list', 'gallery', 'caption']);
    add_theme_support('custom-logo');

    register_nav_menus([
        'primary'      => __('Huvudnavigation', 'makiro'),
        'mobile'       => __('Mobilmeny', 'makiro'),
        'footer_shop'  => __('Footer — Shoppa', 'makiro'),
        'footer_info'  => __('Footer — Information', 'makiro'),
    ]);
}
add_action('after_setup_theme', 'makiro_setup');

function makiro_scripts() {
    wp_enqueue_style('makiro-fonts', 'https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600;700&family=Inter:wght@300;400;500;600&family=JetBrains+Mono:wght@400;500&display=swap', [], null);
    wp_enqueue_style('makiro-style', get_stylesheet_uri(), ['makiro-fonts'], '1.0.2');

    wp_enqueue_script('three-js', 'https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js', [], null, true);
    wp_enqueue_script('three-orbit', 'https://cdn.jsdelivr.net/npm/three@0.128.0/examples/js/controls/OrbitControls.min.js', ['three-js'], null, true);
    wp_enqueue_script('three-stl-loader', 'https://cdn.jsdelivr.net/npm/three@0.128.0/examples/js/loaders/STLLoader.min.js', ['three-js'], null, true);

    wp_enqueue_script('makiro-main', get_template_directory_uri() . '/assets/js/main.js', [], '1.0.2', true);
    wp_enqueue_script('makiro-stl-viewer', get_template_directory_uri() . '/assets/js/stl-viewer.js', ['three-js', 'three-orbit', 'three-stl-loader'], '1.0.2', true);
}
add_action('wp_enqueue_scripts', 'makiro_scripts');

require_once get_template_directory() . '/inc/customizer.php';

/**
 * Admin notice on front page edit screen — guide users to Customizer.
 */
function makiro_front_page_notice() {
    $screen = get_current_screen();
    if ( ! $screen || 'page' !== $screen->id ) return;

    $front_page_id = (int) get_option( 'page_on_front' );
    if ( ! $front_page_id || get_the_ID() !== $front_page_id ) return;

    $customizer_url = admin_url( 'customize.php' );
    ?>
    <div class="notice notice-info is-dismissible" style="border-left-color: #c8ff00;">
        <p><strong>Makiro startsida</strong> — Huvudinnehållet (hero, produkter, kategorier, galleri, recensioner) redigeras via
        <a href="<?php echo esc_url( $customizer_url ); ?>"><strong>Utseende &rarr; Anpassa</strong></a>.
        Eventuellt extra innehåll du lägger till här visas längst ner på sidan.</p>
    </div>
    <?php
}
add_action( 'admin_notices', 'makiro_front_page_notice' );

/** Fallback: primary nav (desktop) */
function makiro_primary_nav_fallback() {
    $links = [
        '#produkter'  => 'Produkter',
        '#kategorier' => 'Kategorier',
        '#3d-viewer'  => '3D Viewer',
        '#process'    => 'Så funkar det',
        '#galleri'    => 'Galleri',
        '#kontakt'    => 'Kontakt',
    ];
    foreach ( $links as $href => $label ) {
        echo '<a href="' . esc_url( $href ) . '" class="nav-link">' . esc_html( $label ) . '</a>';
    }
}

/** Fallback: mobile nav */
function makiro_mobile_nav_fallback() {
    $links = [
        home_url( '/' ) => 'Hem',
        '#produkter'    => 'Produkter',
        '#kategorier'   => 'Kategorier',
        '#3d-viewer'    => '3D Viewer',
        '#process'      => 'Så funkar det',
        '#galleri'      => 'Galleri',
        '#kontakt'      => 'Kontakt',
    ];
    foreach ( $links as $href => $label ) {
        echo '<a href="' . esc_url( $href ) . '" onclick="document.getElementById(\'mobileNav\').classList.remove(\'active\')">' . esc_html( $label ) . '</a>';
    }
}

/** Fallback: footer shop links */
function makiro_footer_shop_fallback() {
    $links = ['Alla produkter', 'Heminredning', '3D Skulpturer', 'Lampor & Belysning', 'Custom Print', 'Presentkort'];
    echo '<ul class="footer-links">';
    foreach ( $links as $label ) {
        echo '<li><a href="#">' . esc_html( $label ) . '</a></li>';
    }
    echo '</ul>';
}

/** Fallback: footer info links */
function makiro_footer_info_fallback() {
    $links = ['Om Makiro', 'Så funkar det', 'Material & Hållbarhet', 'Frakt & Leverans', 'Returer', 'FAQ'];
    echo '<ul class="footer-links">';
    foreach ( $links as $label ) {
        echo '<li><a href="#">' . esc_html( $label ) . '</a></li>';
    }
    echo '</ul>';
}
