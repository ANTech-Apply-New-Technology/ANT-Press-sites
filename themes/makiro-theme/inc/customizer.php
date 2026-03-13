<?php
/**
 * Makiro Theme Customizer
 * All editable fields for end users via Appearance > Customize
 */

function makiro_customizer($wp_customize) {

    // ─── GUIDE ────────────────────────────────────────
    $wp_customize->add_section('makiro_guide', [
        'title'       => '📋 Redigeringsguide',
        'priority'    => 1,
        'description' => '<strong>Så redigerar du startsidan:</strong><br><br>'
            . '• <strong>Utseende → Anpassa</strong> (här!) — Ändra texter, bilder, produkter, recensioner<br>'
            . '• <strong>Utseende → Menyer</strong> — Ändra navigationslänkar (header + footer)<br>'
            . '• <strong>Sidor</strong> — Skapa/redigera undersidor (Om oss, FAQ, etc.)<br>'
            . '• <strong>Inlägg</strong> — Skapa blogginlägg och nyheter<br>'
            . '• <strong>Media</strong> — Ladda upp bilder<br><br>'
            . 'Varje panel nedan motsvarar en sektion på startsidan.',
    ]);

    // Dummy setting so the section shows (WP hides empty sections)
    $wp_customize->add_setting('makiro_guide_placeholder', ['default' => '', 'sanitize_callback' => 'sanitize_text_field']);
    $wp_customize->add_control('makiro_guide_placeholder', ['label' => '', 'section' => 'makiro_guide', 'type' => 'hidden']);

    // ─── HERO ───────────────────────────────────────
    $wp_customize->add_panel('makiro_hero', [
        'title'    => 'Hero-sektion',
        'priority' => 30,
    ]);

    // Hero — Text
    $wp_customize->add_section('makiro_hero_text', [
        'title' => 'Text & knappar',
        'panel' => 'makiro_hero',
    ]);

    $hero_fields = [
        'hero_badge'       => ['Badge-text', 'Nu tillgängligt — Print On Demand'],
        'hero_heading_1'   => ['Rubrik rad 1', 'Designa framtiden'],
        'hero_heading_2'   => ['Rubrik rad 2 (highlight)', '3D-print'],
        'hero_heading_2_prefix' => ['Rubrik rad 2 (före highlight)', 'med '],
        'hero_description' => ['Beskrivning', 'Från idé till verklighet. Makiro AB skapar unika 3D-printade produkter och handplockad heminredning som förändrar hur du upplever ditt hem.'],
        'hero_btn_primary' => ['Primär knapp', 'Utforska produkter'],
        'hero_btn_secondary' => ['Sekundär knapp', 'Så funkar det'],
    ];

    foreach ($hero_fields as $id => $config) {
        $wp_customize->add_setting("makiro_{$id}", ['default' => $config[1], 'sanitize_callback' => 'sanitize_text_field']);
        $wp_customize->add_control("makiro_{$id}", ['label' => $config[0], 'section' => 'makiro_hero_text', 'type' => 'text']);
    }

    // Hero — Stats
    $wp_customize->add_section('makiro_hero_stats', [
        'title' => 'Statistik',
        'panel' => 'makiro_hero',
    ]);

    for ($i = 1; $i <= 3; $i++) {
        $defaults = [
            1 => ['2,400+', 'Produkter sålda'],
            2 => ['98%', 'Nöjda kunder'],
            3 => ['48h', 'Leveranstid'],
        ];
        $wp_customize->add_setting("makiro_stat_{$i}_value", ['default' => $defaults[$i][0], 'sanitize_callback' => 'sanitize_text_field']);
        $wp_customize->add_control("makiro_stat_{$i}_value", ['label' => "Statistik {$i} — värde", 'section' => 'makiro_hero_stats', 'type' => 'text']);
        $wp_customize->add_setting("makiro_stat_{$i}_label", ['default' => $defaults[$i][1], 'sanitize_callback' => 'sanitize_text_field']);
        $wp_customize->add_control("makiro_stat_{$i}_label", ['label' => "Statistik {$i} — etikett", 'section' => 'makiro_hero_stats', 'type' => 'text']);
    }

    // Hero — Images
    $wp_customize->add_section('makiro_hero_images', [
        'title' => 'Bilder',
        'panel' => 'makiro_hero',
    ]);

    $wp_customize->add_setting('makiro_hero_bg_image', ['default' => 'https://images.unsplash.com/photo-1631630259742-c0f0b17c6c10?w=1920&q=80', 'sanitize_callback' => 'esc_url_raw']);
    $wp_customize->add_control(new WP_Customize_Image_Control($wp_customize, 'makiro_hero_bg_image', ['label' => 'Bakgrundsbild', 'section' => 'makiro_hero_images']));

    $wp_customize->add_setting('makiro_hero_product_image', ['default' => 'https://images.unsplash.com/photo-1581783898377-1c85bf937427?w=800&q=80', 'sanitize_callback' => 'esc_url_raw']);
    $wp_customize->add_control(new WP_Customize_Image_Control($wp_customize, 'makiro_hero_product_image', ['label' => 'Produktbild (höger)', 'section' => 'makiro_hero_images']));

    // Hero — Float cards
    $wp_customize->add_section('makiro_hero_floats', [
        'title' => 'Flytande kort',
        'panel' => 'makiro_hero',
    ]);

    $float_fields = [
        'float_top_label' => ['Övre kort — etikett', 'Just nu'],
        'float_top_value' => ['Övre kort — värde', '15% rabatt'],
        'float_bottom_label' => ['Nedre kort — etikett', 'Trending'],
        'float_bottom_value' => ['Nedre kort — värde', 'Geometric Vase'],
    ];

    foreach ($float_fields as $id => $config) {
        $wp_customize->add_setting("makiro_{$id}", ['default' => $config[1], 'sanitize_callback' => 'sanitize_text_field']);
        $wp_customize->add_control("makiro_{$id}", ['label' => $config[0], 'section' => 'makiro_hero_floats', 'type' => 'text']);
    }

    // ─── TRUST BAR ──────────────────────────────────
    $wp_customize->add_section('makiro_trust_bar', [
        'title'    => 'Trust Bar (rullande text)',
        'priority' => 31,
    ]);

    $trust_defaults = [
        'Fri frakt över 499 kr',
        'Hållbara material',
        'Handgjord finish',
        '30 dagars returrätt',
        'Svensk design',
        'Custom 3D-print',
        'Klimatkompenserad leverans',
        'Swish & Klarna',
    ];

    for ($i = 1; $i <= 8; $i++) {
        $wp_customize->add_setting("makiro_trust_{$i}", ['default' => $trust_defaults[$i - 1], 'sanitize_callback' => 'sanitize_text_field']);
        $wp_customize->add_control("makiro_trust_{$i}", ['label' => "Text {$i}", 'section' => 'makiro_trust_bar', 'type' => 'text']);
    }

    // ─── CATEGORIES ─────────────────────────────────
    $wp_customize->add_panel('makiro_categories', [
        'title'    => 'Kategorier',
        'priority' => 32,
    ]);

    $wp_customize->add_section('makiro_categories_header', [
        'title' => 'Rubrik',
        'panel' => 'makiro_categories',
    ]);

    $wp_customize->add_setting('makiro_cat_title', ['default' => 'Utforska våra kollektioner', 'sanitize_callback' => 'sanitize_text_field']);
    $wp_customize->add_control('makiro_cat_title', ['label' => 'Rubrik', 'section' => 'makiro_categories_header']);

    $wp_customize->add_setting('makiro_cat_subtitle', ['default' => 'Från geometriska skulpturer till funktionell inredning — allt 3D-printat med precision och kärlek.', 'sanitize_callback' => 'sanitize_text_field']);
    $wp_customize->add_control('makiro_cat_subtitle', ['label' => 'Underrubrik', 'section' => 'makiro_categories_header', 'type' => 'textarea']);

    $cat_defaults = [
        1 => ['Heminredning', '42 produkter', 'https://images.unsplash.com/photo-1616486338812-3dadae4b4ace?w=800&q=80', '#'],
        2 => ['3D Skulpturer', '28 produkter', 'https://images.unsplash.com/photo-1558618666-fcd25c85f82e?w=800&q=80', '#'],
        3 => ['Lampor & Belysning', '19 produkter', 'https://images.unsplash.com/photo-1513506003901-1e6a229e2d15?w=800&q=80', '#'],
    ];

    for ($i = 1; $i <= 3; $i++) {
        $wp_customize->add_section("makiro_cat_{$i}", [
            'title' => "Kategori {$i}",
            'panel' => 'makiro_categories',
        ]);

        $wp_customize->add_setting("makiro_cat_{$i}_name", ['default' => $cat_defaults[$i][0], 'sanitize_callback' => 'sanitize_text_field']);
        $wp_customize->add_control("makiro_cat_{$i}_name", ['label' => 'Namn', 'section' => "makiro_cat_{$i}"]);

        $wp_customize->add_setting("makiro_cat_{$i}_count", ['default' => $cat_defaults[$i][1], 'sanitize_callback' => 'sanitize_text_field']);
        $wp_customize->add_control("makiro_cat_{$i}_count", ['label' => 'Antal produkter', 'section' => "makiro_cat_{$i}"]);

        $wp_customize->add_setting("makiro_cat_{$i}_image", ['default' => $cat_defaults[$i][2], 'sanitize_callback' => 'esc_url_raw']);
        $wp_customize->add_control(new WP_Customize_Image_Control($wp_customize, "makiro_cat_{$i}_image", ['label' => 'Bild', 'section' => "makiro_cat_{$i}"]));

        $wp_customize->add_setting("makiro_cat_{$i}_link", ['default' => $cat_defaults[$i][3], 'sanitize_callback' => 'esc_url_raw']);
        $wp_customize->add_control("makiro_cat_{$i}_link", ['label' => 'Länk', 'section' => "makiro_cat_{$i}"]);
    }

    // ─── PRODUCTS ───────────────────────────────────
    $wp_customize->add_panel('makiro_products', [
        'title'    => 'Produkter',
        'priority' => 33,
    ]);

    $wp_customize->add_section('makiro_products_header', [
        'title' => 'Rubrik',
        'panel' => 'makiro_products',
    ]);

    $wp_customize->add_setting('makiro_prod_title', ['default' => 'Bästsäljare just nu', 'sanitize_callback' => 'sanitize_text_field']);
    $wp_customize->add_control('makiro_prod_title', ['label' => 'Rubrik', 'section' => 'makiro_products_header']);

    $prod_defaults = [
        1 => ['Geometric Vase — Pearl White', '349 kr', '', 'Heminredning', 'new', 'Nyhet', 'https://images.unsplash.com/photo-1602028915047-37269d1a73f7?w=600&q=80', '47', '5'],
        2 => ['Wave Shelf — Matte Black', '599 kr', '', 'Heminredning', 'popular', 'Populär', 'https://images.unsplash.com/photo-1532372576444-dda954194ad0?w=600&q=80', '89', '5'],
        3 => ['Nordic Pendant — Warm Glow', '479 kr', '599 kr', 'Lampor & Belysning', 'sale', '-20%', 'https://images.unsplash.com/photo-1513519245088-0e12902e35ca?w=600&q=80', '32', '4'],
        4 => ['Abstract Flow — Ivory', '899 kr', '', '3D Skulpturer', '', '', 'https://images.unsplash.com/photo-1586023492125-27b2c045efd7?w=600&q=80', '63', '5'],
        5 => ['Minimal Planter — Sage', '249 kr', '', 'Heminredning', 'new', 'Nyhet', 'https://images.unsplash.com/photo-1493663284031-b7e3aefcae8e?w=600&q=80', '21', '4'],
        6 => ['Twisted Candle Holder — Duo', '199 kr', '', 'Heminredning', '', '', 'https://images.unsplash.com/photo-1507473885765-e6ed057ab6fe?w=600&q=80', '55', '5'],
        7 => ['Hex Desk Organizer — Carbon', '279 kr', '399 kr', '3D Skulpturer', 'sale', '-30%', 'https://images.unsplash.com/photo-1540932239986-30128078f3c5?w=600&q=80', '38', '4'],
        8 => ['3D Wall Art Panel — Waves', '1 249 kr', '', 'Heminredning', '', '', 'https://images.unsplash.com/photo-1616627547584-bf28cee262db?w=600&q=80', '71', '5'],
    ];

    for ($i = 1; $i <= 8; $i++) {
        $d = $prod_defaults[$i];
        $wp_customize->add_section("makiro_prod_{$i}", ['title' => "Produkt {$i}", 'panel' => 'makiro_products']);

        $fields = [
            "prod_{$i}_name"     => ['Produktnamn', $d[0], 'text'],
            "prod_{$i}_price"    => ['Pris', $d[1], 'text'],
            "prod_{$i}_oldprice" => ['Ordinarie pris (rea)', $d[2], 'text'],
            "prod_{$i}_cat"      => ['Kategori', $d[3], 'text'],
            "prod_{$i}_badge_type" => ['Badge-typ (new/sale/popular eller tomt)', $d[4], 'text'],
            "prod_{$i}_badge_text" => ['Badge-text', $d[5], 'text'],
            "prod_{$i}_reviews"  => ['Antal recensioner', $d[7], 'text'],
            "prod_{$i}_stars"    => ['Stjärnor (1-5)', $d[8], 'text'],
        ];

        // Product link (URL field)
        $wp_customize->add_setting("makiro_prod_{$i}_link", ['default' => '#kontakt', 'sanitize_callback' => 'esc_url_raw']);
        $wp_customize->add_control("makiro_prod_{$i}_link", ['label' => 'Produktlänk (URL)', 'section' => "makiro_prod_{$i}", 'type' => 'url']);

        foreach ($fields as $fid => $fc) {
            $wp_customize->add_setting("makiro_{$fid}", ['default' => $fc[1], 'sanitize_callback' => 'sanitize_text_field']);
            $wp_customize->add_control("makiro_{$fid}", ['label' => $fc[0], 'section' => "makiro_prod_{$i}", 'type' => $fc[2]]);
        }

        $wp_customize->add_setting("makiro_prod_{$i}_image", ['default' => $d[6], 'sanitize_callback' => 'esc_url_raw']);
        $wp_customize->add_control(new WP_Customize_Image_Control($wp_customize, "makiro_prod_{$i}_image", ['label' => 'Produktbild', 'section' => "makiro_prod_{$i}"]));
    }

    // ─── PROCESS ────────────────────────────────────
    $wp_customize->add_panel('makiro_process', [
        'title'    => 'Så funkar det',
        'priority' => 34,
    ]);

    $wp_customize->add_section('makiro_process_header', ['title' => 'Rubrik', 'panel' => 'makiro_process']);
    $wp_customize->add_setting('makiro_process_title', ['default' => 'Från idé till dörren — på 48 timmar', 'sanitize_callback' => 'sanitize_text_field']);
    $wp_customize->add_control('makiro_process_title', ['label' => 'Rubrik', 'section' => 'makiro_process_header']);
    $wp_customize->add_setting('makiro_process_subtitle', ['default' => 'Vi kombinerar avancerad 3D-printing med skandinavisk design för att skapa produkter som är lika unika som du.', 'sanitize_callback' => 'sanitize_text_field']);
    $wp_customize->add_control('makiro_process_subtitle', ['label' => 'Underrubrik', 'section' => 'makiro_process_header', 'type' => 'textarea']);

    $step_defaults = [
        1 => ['Välj design', 'Bläddra i vår kollektion eller ladda upp din egen 3D-fil för custom print.'],
        2 => ['Anpassa', 'Välj material, färg och storlek. Vi optimerar din design för bästa resultat.'],
        3 => ['Vi printar', 'Högprecisions 3D-printing med premium PLA, PETG eller resin-material.'],
        4 => ['Leverans', 'Klimatkompenserad frakt direkt till din dörr inom 48 timmar.'],
    ];

    for ($i = 1; $i <= 4; $i++) {
        $wp_customize->add_section("makiro_step_{$i}", ['title' => "Steg {$i}", 'panel' => 'makiro_process']);
        $wp_customize->add_setting("makiro_step_{$i}_title", ['default' => $step_defaults[$i][0], 'sanitize_callback' => 'sanitize_text_field']);
        $wp_customize->add_control("makiro_step_{$i}_title", ['label' => 'Titel', 'section' => "makiro_step_{$i}"]);
        $wp_customize->add_setting("makiro_step_{$i}_desc", ['default' => $step_defaults[$i][1], 'sanitize_callback' => 'sanitize_text_field']);
        $wp_customize->add_control("makiro_step_{$i}_desc", ['label' => 'Beskrivning', 'section' => "makiro_step_{$i}", 'type' => 'textarea']);
    }

    // ─── 3D VIEWER ──────────────────────────────
    $wp_customize->add_panel('makiro_viewer', [
        'title'    => '3D Viewer',
        'priority' => 34,
    ]);

    $wp_customize->add_section('makiro_viewer_text', ['title' => 'Text', 'panel' => 'makiro_viewer']);

    $viewer_fields = [
        'viewer_title'    => ['Rubrik', 'Se din modell i 3D'],
        'viewer_subtitle' => ['Beskrivning', 'Ladda upp din STL-fil och förhandsgranska den direkt i webbläsaren. Rotera, zooma och inspektera innan du beställer.'],
    ];
    foreach ($viewer_fields as $id => $config) {
        $wp_customize->add_setting("makiro_{$id}", ['default' => $config[1], 'sanitize_callback' => 'sanitize_text_field']);
        $wp_customize->add_control("makiro_{$id}", ['label' => $config[0], 'section' => 'makiro_viewer_text', 'type' => $id === 'viewer_subtitle' ? 'textarea' : 'text']);
    }

    // Viewer features
    $wp_customize->add_section('makiro_viewer_features', ['title' => 'Funktioner', 'panel' => 'makiro_viewer']);
    $vf_defaults = [
        1 => ['STL, OBJ & 3MF', 'Stöd för de vanligaste 3D-formaten'],
        2 => ['Instant förhandsgranskning', 'Rotera, zooma och inspektera i realtid'],
        3 => ['Direkt till print', 'Godkänn och beställ — vi printar inom 48h'],
    ];
    for ($i = 1; $i <= 3; $i++) {
        $wp_customize->add_setting("makiro_vf_{$i}_title", ['default' => $vf_defaults[$i][0], 'sanitize_callback' => 'sanitize_text_field']);
        $wp_customize->add_control("makiro_vf_{$i}_title", ['label' => "Funktion {$i} — titel", 'section' => 'makiro_viewer_features']);
        $wp_customize->add_setting("makiro_vf_{$i}_desc", ['default' => $vf_defaults[$i][1], 'sanitize_callback' => 'sanitize_text_field']);
        $wp_customize->add_control("makiro_vf_{$i}_desc", ['label' => "Funktion {$i} — beskrivning", 'section' => 'makiro_viewer_features']);
    }

    // ─── TESTIMONIALS HEADER ──────────────────────
    $wp_customize->add_section('makiro_testimonials_header', ['title' => 'Rubrik', 'panel' => 'makiro_testimonials', 'priority' => 0]);
    $wp_customize->add_setting('makiro_testimonials_label', ['default' => 'Kundrecensioner', 'sanitize_callback' => 'sanitize_text_field']);
    $wp_customize->add_control('makiro_testimonials_label', ['label' => 'Etikett', 'section' => 'makiro_testimonials_header']);
    $wp_customize->add_setting('makiro_testimonials_title', ['default' => 'Vad våra kunder säger', 'sanitize_callback' => 'sanitize_text_field']);
    $wp_customize->add_control('makiro_testimonials_title', ['label' => 'Rubrik', 'section' => 'makiro_testimonials_header']);

    // ─── SHOWCASE ───────────────────────────────────
    $wp_customize->add_panel('makiro_showcase', [
        'title'    => 'Galleri',
        'priority' => 35,
    ]);

    $wp_customize->add_section('makiro_showcase_header', ['title' => 'Rubrik', 'panel' => 'makiro_showcase']);
    $wp_customize->add_setting('makiro_gallery_title', ['default' => 'Se vad vi skapar', 'sanitize_callback' => 'sanitize_text_field']);
    $wp_customize->add_control('makiro_gallery_title', ['label' => 'Rubrik', 'section' => 'makiro_showcase_header']);
    $wp_customize->add_setting('makiro_gallery_subtitle', ['default' => 'Varje produkt är 3D-printad med omsorg och finishad för hand i vår studio i Stockholm.', 'sanitize_callback' => 'sanitize_text_field']);
    $wp_customize->add_control('makiro_gallery_subtitle', ['label' => 'Underrubrik', 'section' => 'makiro_showcase_header', 'type' => 'textarea']);

    $gallery_defaults = [
        1 => ['Vårkollektion 2026', 'Organiska former inspirerade av nordisk natur', 'https://images.unsplash.com/photo-1558603668-6570496b66f8?w=900&q=80'],
        2 => ['Geometric Series', 'Matematisk precision', 'https://images.unsplash.com/photo-1616046229478-9901c5536a45?w=600&q=80'],
        3 => ['Hem & Miljö', 'I sitt rätta element', 'https://images.unsplash.com/photo-1631679706909-1844bbd07221?w=600&q=80'],
        4 => ['Custom Prints', 'Din idé, vår precision', 'https://images.unsplash.com/photo-1555041469-a586c61ea9bc?w=600&q=80'],
        5 => ['Detaljerna', 'Handfinishad kvalitet', 'https://images.unsplash.com/photo-1618221195710-dd6b41faaea6?w=600&q=80'],
    ];

    for ($i = 1; $i <= 5; $i++) {
        $wp_customize->add_section("makiro_gallery_{$i}", ['title' => "Bild {$i}" . ($i === 1 ? ' (stor)' : ''), 'panel' => 'makiro_showcase']);
        $wp_customize->add_setting("makiro_gallery_{$i}_title", ['default' => $gallery_defaults[$i][0], 'sanitize_callback' => 'sanitize_text_field']);
        $wp_customize->add_control("makiro_gallery_{$i}_title", ['label' => 'Titel', 'section' => "makiro_gallery_{$i}"]);
        $wp_customize->add_setting("makiro_gallery_{$i}_desc", ['default' => $gallery_defaults[$i][1], 'sanitize_callback' => 'sanitize_text_field']);
        $wp_customize->add_control("makiro_gallery_{$i}_desc", ['label' => 'Beskrivning', 'section' => "makiro_gallery_{$i}"]);
        $wp_customize->add_setting("makiro_gallery_{$i}_image", ['default' => $gallery_defaults[$i][2], 'sanitize_callback' => 'esc_url_raw']);
        $wp_customize->add_control(new WP_Customize_Image_Control($wp_customize, "makiro_gallery_{$i}_image", ['label' => 'Bild', 'section' => "makiro_gallery_{$i}"]));
    }

    // ─── TESTIMONIALS ───────────────────────────────
    $wp_customize->add_panel('makiro_testimonials', [
        'title'    => 'Kundrecensioner',
        'priority' => 36,
    ]);

    $testimonial_defaults = [
        1 => ['Kvaliteten på vaserna är otrolig. Man kan verkligen se att varje detalj är genomtänkt. Har redan beställt tre till som presenter!', 'Emma Lindqvist', 'Inredningsdesigner, Stockholm', 'https://images.unsplash.com/photo-1494790108755-2616b3b5a8d5?w=100&q=80'],
        2 => ['Skickade in min egen CAD-fil och fick tillbaka en perfekt printad prototyp inom 48 timmar. Servicen och kommunikationen var i toppklass.', 'Anders Bergström', 'Produktdesigner, Göteborg', 'https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=100&q=80'],
        3 => ['Lamporna från Makiro har helt förändrat stämningen i mitt vardagsrum. Det mjuka ljuset genom de 3D-printade mönstren är magiskt.', 'Sofia Eriksson', 'Arkitekt, Malmö', 'https://images.unsplash.com/photo-1438761681033-6461ffad8d80?w=100&q=80'],
    ];

    for ($i = 1; $i <= 3; $i++) {
        $d = $testimonial_defaults[$i];
        $wp_customize->add_section("makiro_review_{$i}", ['title' => "Recension {$i}", 'panel' => 'makiro_testimonials']);
        $wp_customize->add_setting("makiro_review_{$i}_text", ['default' => $d[0], 'sanitize_callback' => 'sanitize_text_field']);
        $wp_customize->add_control("makiro_review_{$i}_text", ['label' => 'Citat', 'section' => "makiro_review_{$i}", 'type' => 'textarea']);
        $wp_customize->add_setting("makiro_review_{$i}_name", ['default' => $d[1], 'sanitize_callback' => 'sanitize_text_field']);
        $wp_customize->add_control("makiro_review_{$i}_name", ['label' => 'Namn', 'section' => "makiro_review_{$i}"]);
        $wp_customize->add_setting("makiro_review_{$i}_role", ['default' => $d[2], 'sanitize_callback' => 'sanitize_text_field']);
        $wp_customize->add_control("makiro_review_{$i}_role", ['label' => 'Roll/Plats', 'section' => "makiro_review_{$i}"]);
        $wp_customize->add_setting("makiro_review_{$i}_avatar", ['default' => $d[3], 'sanitize_callback' => 'esc_url_raw']);
        $wp_customize->add_control(new WP_Customize_Image_Control($wp_customize, "makiro_review_{$i}_avatar", ['label' => 'Profilbild', 'section' => "makiro_review_{$i}"]));
    }

    // ─── NEWSLETTER ─────────────────────────────────
    $wp_customize->add_section('makiro_newsletter', [
        'title'    => 'Nyhetsbrev',
        'priority' => 37,
    ]);

    $wp_customize->add_setting('makiro_nl_heading', ['default' => 'Få <span class="text-accent">15% rabatt</span> på din första order', 'sanitize_callback' => 'wp_kses_post']);
    $wp_customize->add_control('makiro_nl_heading', ['label' => 'Rubrik (HTML tillåtet)', 'section' => 'makiro_newsletter', 'type' => 'textarea']);
    $wp_customize->add_setting('makiro_nl_text', ['default' => 'Prenumerera på vårt nyhetsbrev för exklusiva erbjudanden, nya releaser och inspiration för ditt hem.', 'sanitize_callback' => 'sanitize_text_field']);
    $wp_customize->add_control('makiro_nl_text', ['label' => 'Beskrivning', 'section' => 'makiro_newsletter', 'type' => 'textarea']);

    // ─── FOOTER ─────────────────────────────────────
    $wp_customize->add_section('makiro_footer', [
        'title'    => 'Footer',
        'priority' => 38,
    ]);

    $wp_customize->add_setting('makiro_footer_desc', ['default' => 'Vi skapar unika 3D-printade produkter och heminredning i Stockholm. Varje produkt är designad med omsorg och tillverkad med precision.', 'sanitize_callback' => 'sanitize_text_field']);
    $wp_customize->add_control('makiro_footer_desc', ['label' => 'Beskrivning', 'section' => 'makiro_footer', 'type' => 'textarea']);
    $wp_customize->add_setting('makiro_footer_email', ['default' => 'hej@makiroab.se', 'sanitize_callback' => 'sanitize_email']);
    $wp_customize->add_control('makiro_footer_email', ['label' => 'E-post', 'section' => 'makiro_footer']);
    $wp_customize->add_setting('makiro_footer_phone', ['default' => '070-123 45 67', 'sanitize_callback' => 'sanitize_text_field']);
    $wp_customize->add_control('makiro_footer_phone', ['label' => 'Telefon', 'section' => 'makiro_footer']);
    $wp_customize->add_setting('makiro_footer_address1', ['default' => 'Studiovägen 12', 'sanitize_callback' => 'sanitize_text_field']);
    $wp_customize->add_control('makiro_footer_address1', ['label' => 'Adress rad 1', 'section' => 'makiro_footer']);
    $wp_customize->add_setting('makiro_footer_address2', ['default' => '114 55 Stockholm', 'sanitize_callback' => 'sanitize_text_field']);
    $wp_customize->add_control('makiro_footer_address2', ['label' => 'Adress rad 2', 'section' => 'makiro_footer']);

    $socials = ['instagram' => '#', 'facebook' => '#', 'pinterest' => '#', 'tiktok' => '#'];
    foreach ($socials as $name => $default) {
        $wp_customize->add_setting("makiro_social_{$name}", ['default' => $default, 'sanitize_callback' => 'esc_url_raw']);
        $wp_customize->add_control("makiro_social_{$name}", ['label' => ucfirst($name) . ' URL', 'section' => 'makiro_footer']);
    }
}
add_action('customize_register', 'makiro_customizer');

/** Helper: get theme mod with esc */
function m($key, $default = '') {
    return esc_html(get_theme_mod("makiro_{$key}", $default));
}

function m_url($key, $default = '') {
    return esc_url(get_theme_mod("makiro_{$key}", $default));
}

function m_raw($key, $default = '') {
    return wp_kses_post(get_theme_mod("makiro_{$key}", $default));
}
