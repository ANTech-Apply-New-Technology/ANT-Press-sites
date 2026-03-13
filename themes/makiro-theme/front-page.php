<?php get_header(); ?>

<?php
// SVG helpers
$arrow_svg = '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M7 17L17 7M17 7H7M17 7v10"/></svg>';
$star_full = '<svg width="12" height="12" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>';
$star_empty = '<svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="1" viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>';

function makiro_stars($count) {
    global $star_full, $star_empty;
    $out = '';
    for ($i = 1; $i <= 5; $i++) {
        $out .= $i <= $count ? $star_full : $star_empty;
    }
    return $out;
}
?>

<!-- HERO -->
<section class="hero">
  <div class="hero-bg">
    <img src="<?php echo m_url('hero_bg_image', 'https://images.unsplash.com/photo-1631630259742-c0f0b17c6c10?w=1920&q=80'); ?>" alt="3D Printing">
    <div class="hero-grid"></div>
  </div>
  <div class="hero-content">
    <div class="hero-text">
      <div class="hero-badge">
        <span class="hero-badge-dot"></span>
        <?php echo m('hero_badge', 'Nu tillgängligt — Print On Demand'); ?>
      </div>
      <h1>
        <span class="line"><?php echo m('hero_heading_1', 'Designa framtiden'); ?></span>
        <span class="line"><?php echo m('hero_heading_2_prefix', 'med '); ?><span class="highlight"><?php echo m('hero_heading_2', '3D-print'); ?></span></span>
      </h1>
      <p class="hero-description"><?php echo m('hero_description', 'Från idé till verklighet. Makiro AB skapar unika 3D-printade produkter och handplockad heminredning som förändrar hur du upplever ditt hem.'); ?></p>
      <div class="hero-actions">
        <a href="#produkter" class="btn btn-primary">
          <?php echo m('hero_btn_primary', 'Utforska produkter'); ?>
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
        </a>
        <a href="#process" class="btn btn-outline"><?php echo m('hero_btn_secondary', 'Så funkar det'); ?></a>
      </div>
      <div class="hero-stats">
        <?php for ($i = 1; $i <= 3; $i++): $dv = ['2,400+','98%','48h']; $dl = ['Produkter sålda','Nöjda kunder','Leveranstid']; ?>
        <div class="hero-stat">
          <span class="hero-stat-value"><?php echo m("stat_{$i}_value", $dv[$i-1]); ?></span>
          <span class="hero-stat-label"><?php echo m("stat_{$i}_label", $dl[$i-1]); ?></span>
        </div>
        <?php endfor; ?>
      </div>
    </div>
    <div class="hero-visual">
      <div class="hero-image-wrapper">
        <img src="<?php echo m_url('hero_product_image', 'https://images.unsplash.com/photo-1581783898377-1c85bf937427?w=800&q=80'); ?>" alt="3D printed product showcase">
      </div>
      <div class="hero-float-card top-right">
        <div>
          <div class="float-card-label"><?php echo m('float_top_label', 'Just nu'); ?></div>
          <div class="float-card-value"><?php echo m('float_top_value', '15% rabatt'); ?></div>
        </div>
      </div>
      <div class="hero-float-card bottom-left">
        <div class="float-card-label"><?php echo m('float_bottom_label', 'Trending'); ?></div>
        <div class="float-card-value"><?php echo m('float_bottom_value', 'Geometric Vase'); ?></div>
      </div>
    </div>
  </div>
</section>

<!-- TRUST BAR -->
<div class="trust-bar">
  <div class="marquee">
    <?php
    $trust_items = [];
    $trust_defaults = ['Fri frakt över 499 kr','Hållbara material','Handgjord finish','30 dagars returrätt','Svensk design','Custom 3D-print','Klimatkompenserad leverans','Swish & Klarna'];
    for ($i = 1; $i <= 8; $i++) {
        $trust_items[] = m("trust_{$i}", $trust_defaults[$i-1]);
    }
    for ($dup = 0; $dup < 2; $dup++): ?>
    <div class="marquee-content"<?php echo $dup ? ' aria-hidden="true"' : ''; ?>>
      <?php foreach ($trust_items as $item): ?>
      <span class="marquee-item"><span class="marquee-dot"></span> <?php echo $item; ?></span>
      <?php endforeach; ?>
    </div>
    <?php endfor; ?>
  </div>
</div>

<!-- CATEGORIES -->
<section class="categories section" id="kategorier">
  <div class="container">
    <div class="section-label">Kategorier</div>
    <h2 class="section-title"><?php echo m('cat_title', 'Utforska våra kollektioner'); ?></h2>
    <p class="section-subtitle"><?php echo m('cat_subtitle', 'Från geometriska skulpturer till funktionell inredning — allt 3D-printat med precision och kärlek.'); ?></p>
    <div class="categories-grid">
      <?php
      $cat_img_defaults = [
          'https://images.unsplash.com/photo-1616486338812-3dadae4b4ace?w=800&q=80',
          'https://images.unsplash.com/photo-1558618666-fcd25c85f82e?w=800&q=80',
          'https://images.unsplash.com/photo-1513506003901-1e6a229e2d15?w=800&q=80',
      ];
      $cat_name_defaults = ['Heminredning', '3D Skulpturer', 'Lampor & Belysning'];
      $cat_count_defaults = ['42 produkter', '28 produkter', '19 produkter'];
      for ($i = 1; $i <= 3; $i++): ?>
      <a href="<?php echo m_url("cat_{$i}_link", '#'); ?>" class="category-card animate-in">
        <img src="<?php echo m_url("cat_{$i}_image", $cat_img_defaults[$i-1]); ?>" alt="<?php echo m("cat_{$i}_name", $cat_name_defaults[$i-1]); ?>">
        <div class="category-overlay">
          <h3 class="category-name"><?php echo m("cat_{$i}_name", $cat_name_defaults[$i-1]); ?></h3>
          <span class="category-count"><?php echo m("cat_{$i}_count", $cat_count_defaults[$i-1]); ?></span>
        </div>
        <div class="category-arrow"><?php echo $arrow_svg; ?></div>
      </a>
      <?php endfor; ?>
    </div>
  </div>
</section>

<!-- 3D VIEWER -->
<section class="stl-viewer-section section" id="3d-viewer">
  <div class="container">
    <div class="viewer-layout">
      <div class="viewer-info">
        <div class="section-label">3D Viewer</div>
        <h2 class="section-title"><?php echo m('viewer_title', 'Se din modell i 3D'); ?></h2>
        <p class="section-subtitle"><?php echo m('viewer_subtitle', 'Ladda upp din STL-fil och förhandsgranska den direkt i webbläsaren. Rotera, zooma och inspektera innan du beställer.'); ?></p>
        <div class="viewer-features">
          <?php
          $vf_icons = [
              '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="var(--color-accent)" stroke-width="1.5"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/></svg>',
              '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="var(--color-accent)" stroke-width="1.5"><path d="M12 22c5.523 0 10-4.477 10-10S17.523 2 12 2 2 6.477 2 12s4.477 10 10 10z"/><path d="m9 12 2 2 4-4"/></svg>',
              '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="var(--color-accent)" stroke-width="1.5"><rect x="1" y="3" width="15" height="13" rx="2"/><path d="M16 8h4l3 3v5a1 1 0 0 1-1 1h-1"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg>',
          ];
          $vf_title_defaults = ['STL, OBJ &amp; 3MF', 'Instant förhandsgranskning', 'Direkt till print'];
          $vf_desc_defaults = ['Stöd för de vanligaste 3D-formaten', 'Rotera, zooma och inspektera i realtid', 'Godkänn och beställ — vi printar inom 48h'];
          for ($i = 1; $i <= 3; $i++): ?>
          <div class="viewer-feature">
            <div class="viewer-feature-icon"><?php echo $vf_icons[$i-1]; ?></div>
            <div><h4><?php echo m("vf_{$i}_title", $vf_title_defaults[$i-1]); ?></h4><p><?php echo m("vf_{$i}_desc", $vf_desc_defaults[$i-1]); ?></p></div>
          </div>
          <?php endfor; ?>
        </div>
        <div class="viewer-materials">
          <span class="viewer-material-tag">PLA</span><span class="viewer-material-tag">PETG</span><span class="viewer-material-tag">ABS</span><span class="viewer-material-tag">Resin</span><span class="viewer-material-tag">Nylon</span><span class="viewer-material-tag">TPU</span>
        </div>
      </div>
      <div class="viewer-canvas-wrapper">
        <div class="viewer-canvas-container" id="stlViewerContainer">
          <canvas id="stlCanvas"></canvas>
          <div class="viewer-overlay" id="viewerOverlay">
            <div class="viewer-drop-zone" id="dropZone">
              <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="var(--color-accent)" stroke-width="1"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/><path d="M3.27 6.96 12 12.01l8.73-5.05M12 22.08V12"/></svg>
              <p class="drop-title">Dra &amp; släpp din STL-fil här</p>
              <p class="drop-subtitle">eller</p>
              <label for="stlFileInput" class="btn btn-primary btn-sm" style="cursor:pointer;">Välj fil</label>
              <input type="file" id="stlFileInput" accept=".stl,.obj" style="display:none;">
              <p class="drop-hint">Max 50 MB — .stl eller .obj</p>
            </div>
          </div>
          <div class="viewer-controls" id="viewerControls" style="display:none;">
            <button class="viewer-ctrl-btn" id="btnResetView" title="Återställ vy"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 4v6h6M23 20v-6h-6"/><path d="M20.49 9A9 9 0 0 0 5.64 5.64L1 10m22 4l-4.64 4.36A9 9 0 0 1 3.51 15"/></svg></button>
            <button class="viewer-ctrl-btn" id="btnWireframe" title="Wireframe"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/></svg></button>
            <button class="viewer-ctrl-btn" id="btnAutoRotate" title="Auto-rotera"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M23 4v6h-6"/><path d="M20.49 15A9 9 0 1 1 21 6.35L23 4"/></svg></button>
            <div class="viewer-color-picker">
              <button class="color-dot active" data-color="#c8ff00" style="background:#c8ff00;" title="Neon"></button>
              <button class="color-dot" data-color="#ffffff" style="background:#ffffff;" title="Vit"></button>
              <button class="color-dot" data-color="#1a1a1a" style="background:#1a1a1a; border:1px solid #444;" title="Svart"></button>
              <button class="color-dot" data-color="#4a90d9" style="background:#4a90d9;" title="Blå"></button>
              <button class="color-dot" data-color="#e74c3c" style="background:#e74c3c;" title="Röd"></button>
            </div>
          </div>
          <div class="viewer-model-info" id="modelInfo" style="display:none;"><span id="modelName"></span><span id="modelSize"></span></div>
        </div>
        <div class="viewer-cta" id="viewerCta" style="display:none;">
          <div class="viewer-price-estimate"><span class="viewer-price-label">Uppskattat pris från</span><span class="viewer-price-value" id="estimatedPrice">149 kr</span></div>
          <a href="#kontakt" class="btn btn-primary">Beställ print</a>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- PRODUCTS -->
<section class="featured-products section" id="produkter">
  <div class="container">
    <div class="products-header">
      <div>
        <div class="section-label">Populära produkter</div>
        <h2 class="section-title"><?php echo m('prod_title', 'Bästsäljare just nu'); ?></h2>
      </div>
      <div class="products-tabs">
        <button class="product-tab active">Alla</button>
        <button class="product-tab">Inredning</button>
        <button class="product-tab">Skulpturer</button>
        <button class="product-tab">Lampor</button>
      </div>
    </div>
    <div class="products-grid">
      <?php
      $prod_img_defaults = [
          'https://images.unsplash.com/photo-1602028915047-37269d1a73f7?w=600&q=80',
          'https://images.unsplash.com/photo-1532372576444-dda954194ad0?w=600&q=80',
          'https://images.unsplash.com/photo-1513519245088-0e12902e35ca?w=600&q=80',
          'https://images.unsplash.com/photo-1586023492125-27b2c045efd7?w=600&q=80',
          'https://images.unsplash.com/photo-1493663284031-b7e3aefcae8e?w=600&q=80',
          'https://images.unsplash.com/photo-1507473885765-e6ed057ab6fe?w=600&q=80',
          'https://images.unsplash.com/photo-1540932239986-30128078f3c5?w=600&q=80',
          'https://images.unsplash.com/photo-1616627547584-bf28cee262db?w=600&q=80',
      ];
      $prod_name_defaults = ['Geometric Vase — Pearl White','Wave Shelf — Matte Black','Nordic Pendant — Warm Glow','Abstract Flow — Ivory','Minimal Planter — Sage','Twisted Candle Holder — Duo','Hex Desk Organizer — Carbon','3D Wall Art Panel — Waves'];
      $prod_price_defaults = ['349 kr','599 kr','479 kr','899 kr','249 kr','199 kr','279 kr','1 249 kr'];
      $prod_oldprice_defaults = ['','','599 kr','','','','399 kr',''];
      $prod_cat_defaults = ['Heminredning','Heminredning','Lampor & Belysning','3D Skulpturer','Heminredning','Heminredning','3D Skulpturer','Heminredning'];
      $prod_badge_type_defaults = ['new','popular','sale','','new','','sale',''];
      $prod_badge_text_defaults = ['Nyhet','Populär','-20%','','Nyhet','','-30%',''];
      $prod_reviews_defaults = ['47','89','32','63','21','55','38','71'];
      $prod_stars_defaults = ['5','5','4','5','4','5','4','5'];

      for ($i = 1; $i <= 8; $i++):
          $name = m("prod_{$i}_name", $prod_name_defaults[$i-1]);
          $price = m("prod_{$i}_price", $prod_price_defaults[$i-1]);
          $oldprice = m("prod_{$i}_oldprice", $prod_oldprice_defaults[$i-1]);
          $cat = m("prod_{$i}_cat", $prod_cat_defaults[$i-1]);
          $badge_type = m("prod_{$i}_badge_type", $prod_badge_type_defaults[$i-1]);
          $badge_text = m("prod_{$i}_badge_text", $prod_badge_text_defaults[$i-1]);
          $image = m_url("prod_{$i}_image", $prod_img_defaults[$i-1]);
          $reviews = m("prod_{$i}_reviews", $prod_reviews_defaults[$i-1]);
          $stars = (int) m("prod_{$i}_stars", $prod_stars_defaults[$i-1]);
          $link = m_url("prod_{$i}_link", '#kontakt');
      ?>
      <a href="<?php echo esc_url( $link ); ?>" class="product-card animate-in">
        <?php if ($badge_type): ?><span class="product-badge <?php echo $badge_type; ?>"><?php echo $badge_text; ?></span><?php endif; ?>
        <div class="product-image">
          <img src="<?php echo $image; ?>" alt="<?php echo $name; ?>">
          <div class="product-quick-actions"><span class="quick-action-btn">Snabbvy</span><span class="quick-action-btn accent">Köp</span></div>
        </div>
        <div class="product-info">
          <div class="product-category-label"><?php echo $cat; ?></div>
          <h3 class="product-name"><?php echo $name; ?></h3>
          <div class="product-price-row">
            <span class="product-price"><?php echo $price; ?><?php if ($oldprice): ?> <span class="old-price"><?php echo $oldprice; ?></span><?php endif; ?></span>
            <div class="product-rating"><?php echo makiro_stars($stars); ?> <span>(<?php echo $reviews; ?>)</span></div>
          </div>
        </div>
      </a>
      <?php endfor; ?>
    </div>
    <div style="text-align: center; margin-top: var(--space-2xl);">
      <a href="#" class="btn btn-outline">Visa alla produkter <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14M12 5l7 7-7 7"/></svg></a>
    </div>
  </div>
</section>

<!-- HOW IT WORKS -->
<section class="process section" id="process">
  <div class="container">
    <div style="text-align: center;">
      <div class="section-label" style="justify-content: center;"><span>Så funkar det</span></div>
      <h2 class="section-title"><?php echo m('process_title', 'Från idé till dörren — på 48 timmar'); ?></h2>
      <p class="section-subtitle" style="margin: 0 auto var(--space-2xl);"><?php echo m('process_subtitle', 'Vi kombinerar avancerad 3D-printing med skandinavisk design för att skapa produkter som är lika unika som du.'); ?></p>
    </div>
    <div class="process-grid">
      <?php
      $step_icons = [
          '<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="var(--color-accent)" stroke-width="1.5"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>',
          '<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="var(--color-accent)" stroke-width="1.5"><path d="M12 22c5.523 0 10-4.477 10-10S17.523 2 12 2 2 6.477 2 12s4.477 10 10 10z"/><path d="m9 12 2 2 4-4"/></svg>',
          '<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="var(--color-accent)" stroke-width="1.5"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/></svg>',
          '<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="var(--color-accent)" stroke-width="1.5"><rect x="1" y="3" width="15" height="13" rx="2"/><path d="M16 8h4l3 3v5a1 1 0 0 1-1 1h-1"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg>',
      ];
      $step_title_defaults = ['Välj design','Anpassa','Vi printar','Leverans'];
      $step_desc_defaults = ['Bläddra i vår kollektion eller ladda upp din egen 3D-fil för custom print.','Välj material, färg och storlek. Vi optimerar din design för bästa resultat.','Högprecisions 3D-printing med premium PLA, PETG eller resin-material.','Klimatkompenserad frakt direkt till din dörr inom 48 timmar.'];
      for ($i = 1; $i <= 4; $i++): ?>
      <div class="process-step animate-in">
        <div class="process-icon"><?php echo $step_icons[$i-1]; ?></div>
        <div class="process-number"><?php echo str_pad($i, 2, '0', STR_PAD_LEFT); ?></div>
        <h3 class="process-title"><?php echo m("step_{$i}_title", $step_title_defaults[$i-1]); ?></h3>
        <p class="process-desc"><?php echo m("step_{$i}_desc", $step_desc_defaults[$i-1]); ?></p>
      </div>
      <?php endfor; ?>
    </div>
  </div>
</section>

<!-- SHOWCASE -->
<section class="showcase section" id="galleri">
  <div class="container">
    <div class="section-label">Galleri</div>
    <h2 class="section-title"><?php echo m('gallery_title', 'Se vad vi skapar'); ?></h2>
    <p class="section-subtitle"><?php echo m('gallery_subtitle', 'Varje produkt är 3D-printad med omsorg och finishad för hand i vår studio i Stockholm.'); ?></p>
    <div class="showcase-grid">
      <?php
      $gallery_img_defaults = [
          'https://images.unsplash.com/photo-1558603668-6570496b66f8?w=900&q=80',
          'https://images.unsplash.com/photo-1616046229478-9901c5536a45?w=600&q=80',
          'https://images.unsplash.com/photo-1631679706909-1844bbd07221?w=600&q=80',
          'https://images.unsplash.com/photo-1555041469-a586c61ea9bc?w=600&q=80',
          'https://images.unsplash.com/photo-1618221195710-dd6b41faaea6?w=600&q=80',
      ];
      $gallery_title_defaults = ['Vårkollektion 2026','Geometric Series','Hem & Miljö','Custom Prints','Detaljerna'];
      $gallery_desc_defaults = ['Organiska former inspirerade av nordisk natur','Matematisk precision','I sitt rätta element','Din idé, vår precision','Handfinishad kvalitet'];
      for ($i = 1; $i <= 5; $i++): ?>
      <div class="showcase-item<?php echo $i === 1 ? ' large' : ''; ?> animate-in">
        <img src="<?php echo m_url("gallery_{$i}_image", $gallery_img_defaults[$i-1]); ?>" alt="<?php echo m("gallery_{$i}_title", $gallery_title_defaults[$i-1]); ?>">
        <div class="showcase-caption">
          <h3><?php echo m("gallery_{$i}_title", $gallery_title_defaults[$i-1]); ?></h3>
          <p><?php echo m("gallery_{$i}_desc", $gallery_desc_defaults[$i-1]); ?></p>
        </div>
      </div>
      <?php endfor; ?>
    </div>
  </div>
</section>

<!-- TESTIMONIALS -->
<section class="testimonials section">
  <div class="container">
    <div style="text-align: center;">
      <div class="section-label" style="justify-content: center;"><?php echo m('testimonials_label', 'Kundrecensioner'); ?></div>
      <h2 class="section-title"><?php echo m('testimonials_title', 'Vad våra kunder säger'); ?></h2>
    </div>
    <div class="testimonials-grid">
      <?php
      $rev_text_defaults = ['Kvaliteten på vaserna är otrolig. Man kan verkligen se att varje detalj är genomtänkt. Har redan beställt tre till som presenter!','Skickade in min egen CAD-fil och fick tillbaka en perfekt printad prototyp inom 48 timmar. Servicen och kommunikationen var i toppklass.','Lamporna från Makiro har helt förändrat stämningen i mitt vardagsrum. Det mjuka ljuset genom de 3D-printade mönstren är magiskt.'];
      $rev_name_defaults = ['Emma Lindqvist','Anders Bergström','Sofia Eriksson'];
      $rev_role_defaults = ['Inredningsdesigner, Stockholm','Produktdesigner, Göteborg','Arkitekt, Malmö'];
      $rev_avatar_defaults = ['https://images.unsplash.com/photo-1494790108755-2616b3b5a8d5?w=100&q=80','https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=100&q=80','https://images.unsplash.com/photo-1438761681033-6461ffad8d80?w=100&q=80'];
      for ($i = 1; $i <= 3; $i++): ?>
      <div class="testimonial-card animate-in">
        <div class="testimonial-stars"><?php echo makiro_stars(5); ?></div>
        <p class="testimonial-text">"<?php echo m("review_{$i}_text", $rev_text_defaults[$i-1]); ?>"</p>
        <div class="testimonial-author">
          <div class="testimonial-avatar"><img src="<?php echo m_url("review_{$i}_avatar", $rev_avatar_defaults[$i-1]); ?>" alt="<?php echo m("review_{$i}_name", $rev_name_defaults[$i-1]); ?>"></div>
          <div>
            <div class="testimonial-name"><?php echo m("review_{$i}_name", $rev_name_defaults[$i-1]); ?></div>
            <div class="testimonial-role"><?php echo m("review_{$i}_role", $rev_role_defaults[$i-1]); ?></div>
          </div>
        </div>
      </div>
      <?php endfor; ?>
    </div>
  </div>
</section>

<!-- NEWSLETTER -->
<section class="newsletter">
  <div class="container">
    <div class="newsletter-card">
      <div class="newsletter-content">
        <h2><?php echo m_raw('nl_heading', 'Få <span class="text-accent">15% rabatt</span> på din första order'); ?></h2>
        <p><?php echo m('nl_text', 'Prenumerera på vårt nyhetsbrev för exklusiva erbjudanden, nya releaser och inspiration för ditt hem.'); ?></p>
        <form class="newsletter-form" onsubmit="event.preventDefault();">
          <input type="email" class="newsletter-input" placeholder="Din e-postadress">
          <button type="submit" class="btn btn-primary btn-sm">Prenumerera</button>
        </form>
      </div>
    </div>
  </div>
</section>

<?php
// Extra content from Pages editor (if customer adds content to the homepage)
if ( have_posts() ) :
    while ( have_posts() ) : the_post();
        $content = get_the_content();
        if ( trim( $content ) ) : ?>
<section class="page-extra section">
  <div class="container">
    <div class="page-content">
      <div class="entry-content">
        <?php the_content(); ?>
      </div>
    </div>
  </div>
</section>
<?php
        endif;
    endwhile;
endif;
?>

<?php get_footer(); ?>
