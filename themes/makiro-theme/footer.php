<footer class="site-footer" id="kontakt">
  <div class="footer-main">
    <div class="container">
      <div class="footer-grid">
        <div class="footer-brand">
          <a href="<?php echo home_url(); ?>" class="site-logo">
            <div class="logo-mark">M</div>
            <span>makiro</span>
          </a>
          <p><?php echo m('footer_desc', 'Vi skapar unika 3D-printade produkter och heminredning i Stockholm.'); ?></p>
          <div class="footer-social">
            <a href="<?php echo m_url('social_instagram', '#'); ?>" class="social-link" aria-label="Instagram">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="2" y="2" width="20" height="20" rx="5"/><circle cx="12" cy="12" r="5"/><circle cx="17.5" cy="6.5" r="1.5" fill="currentColor" stroke="none"/></svg>
            </a>
            <a href="<?php echo m_url('social_facebook', '#'); ?>" class="social-link" aria-label="Facebook">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/></svg>
            </a>
            <a href="<?php echo m_url('social_pinterest', '#'); ?>" class="social-link" aria-label="Pinterest">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M8 12a4 4 0 1 1 8 0c0 3-2 6-4 8"/><path d="M12 12l-2 8"/><circle cx="12" cy="12" r="10"/></svg>
            </a>
            <a href="<?php echo m_url('social_tiktok', '#'); ?>" class="social-link" aria-label="TikTok">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M9 12a4 4 0 1 0 4 4V4a5 5 0 0 0 5 5"/></svg>
            </a>
          </div>
        </div>
        <div>
          <h4 class="footer-heading">Shoppa</h4>
          <?php
          wp_nav_menu([
              'theme_location' => 'footer_shop',
              'container'      => false,
              'menu_class'     => 'footer-links',
              'depth'          => 1,
              'fallback_cb'    => 'makiro_footer_shop_fallback',
          ]);
          ?>
        </div>
        <div>
          <h4 class="footer-heading">Information</h4>
          <?php
          wp_nav_menu([
              'theme_location' => 'footer_info',
              'container'      => false,
              'menu_class'     => 'footer-links',
              'depth'          => 1,
              'fallback_cb'    => 'makiro_footer_info_fallback',
          ]);
          ?>
        </div>
        <div>
          <h4 class="footer-heading">Kontakt</h4>
          <ul class="footer-links">
            <li><a href="mailto:<?php echo m('footer_email', 'hej@makiroab.se'); ?>"><?php echo m('footer_email', 'hej@makiroab.se'); ?></a></li>
            <li><a href="tel:<?php echo m('footer_phone', '070-123 45 67'); ?>"><?php echo m('footer_phone', '070-123 45 67'); ?></a></li>
            <li><?php echo m('footer_address1', 'Studiovägen 12'); ?></li>
            <li><?php echo m('footer_address2', '114 55 Stockholm'); ?></li>
          </ul>
          <div style="margin-top: var(--space-lg);">
            <h4 class="footer-heading">Betalning</h4>
            <div style="display: flex; gap: var(--space-sm); flex-wrap: wrap;">
              <?php foreach (['Swish', 'Klarna', 'Visa', 'MC'] as $pay): ?>
              <span style="background: var(--color-bg-card); border: 1px solid var(--color-border); border-radius: var(--radius-sm); padding: 4px 10px; font-size: 0.6875rem; color: var(--color-text-muted);"><?php echo $pay; ?></span>
              <?php endforeach; ?>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="footer-bottom">
    <div class="container">
      <div class="footer-bottom-inner">
        <span>&copy; <?php echo date('Y'); ?> Makiro AB. Alla rättigheter förbehållna.</span>
        <div class="footer-bottom-links">
          <a href="#">Integritetspolicy</a>
          <a href="#">Köpvillkor</a>
          <a href="#">Cookies</a>
        </div>
      </div>
    </div>
  </div>
</footer>
<?php wp_footer(); ?>
</body>
</html>
