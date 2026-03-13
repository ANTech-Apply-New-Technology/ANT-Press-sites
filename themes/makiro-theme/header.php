<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
  <meta charset="<?php bloginfo('charset'); ?>">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <?php wp_head(); ?>
  <noscript><style>.animate-in { opacity: 1 !important; transform: none !important; }</style></noscript>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<div class="mobile-nav" id="mobileNav">
  <button class="mobile-nav-close" onclick="document.getElementById('mobileNav').classList.remove('active')">
    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6 6 18M6 6l12 12"/></svg>
  </button>
  <?php
  wp_nav_menu([
      'theme_location' => 'mobile',
      'container'      => false,
      'items_wrap'     => '%3$s',
      'depth'          => 1,
      'fallback_cb'    => 'makiro_mobile_nav_fallback',
  ]);
  ?>
</div>

<header class="site-header" id="siteHeader">
  <div class="header-inner">
    <a href="<?php echo home_url(); ?>" class="site-logo">
      <div class="logo-mark">M</div>
      <span>makiro</span>
    </a>
    <nav class="nav-links">
      <?php
      wp_nav_menu([
          'theme_location' => 'primary',
          'container'      => false,
          'items_wrap'     => '%3$s',
          'depth'          => 1,
          'fallback_cb'    => 'makiro_primary_nav_fallback',
      ]);
      ?>
    </nav>
    <div class="header-actions">
      <?php
      $cart_url = class_exists( 'WooCommerce' ) ? wc_get_cart_url() : '#produkter';
      ?>
      <a href="<?php echo esc_url( $cart_url ); ?>" class="btn-icon cart-btn" aria-label="Varukorg">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4zM3 6h18M16 10a4 4 0 0 1-8 0"/></svg>
        <?php if ( class_exists( 'WooCommerce' ) ) :
          $count = WC()->cart->get_cart_contents_count();
          if ( $count > 0 ) : ?>
            <span class="cart-count"><?php echo esc_html( $count ); ?></span>
          <?php endif;
        endif; ?>
      </a>
      <button class="menu-toggle" onclick="document.getElementById('mobileNav').classList.add('active')" aria-label="Meny">
        <span></span><span></span><span></span>
      </button>
    </div>
  </div>
</header>
