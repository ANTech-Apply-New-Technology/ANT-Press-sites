# Makiro Theme — Developer Guide

Classic PHP WordPress theme for 3D print e-commerce. Dark design with lime accent, Three.js STL viewer, and Customizer-driven homepage.

## Design Language

- **Typography**: Space Grotesk (display), Inter (body), JetBrains Mono (mono)
- **Colors**: Lime accent `#c8ff00` on dark `#0a0a0a`. Cards `#1a1a1a`, borders `#2a2a2a`
- **Buttons**: Primary = lime filled, secondary = outline with lime hover
- **Layout**: Full-width sections, 4-column product grid, dark card UI
- **Header**: Inline nav links (desktop), hamburger menu (mobile), always-visible cart icon

## Architecture

```
makiro-theme/
├── style.css              # Design tokens (:root vars) + all component CSS (~1900 lines)
├── functions.php          # Theme setup, scripts, nav menus (4 locations), fallback callbacks
├── front-page.php         # Homepage — Customizer-driven sections + the_content()
├── header.php             # Site header + mobile nav overlay
├── footer.php             # 4-column footer with contact, social, payment
├── page.php               # Generic page template
├── single.php             # Blog post template
├── archive.php            # Post archive grid
├── 404.php                # Not found page
├── index.php              # WordPress fallback (required)
├── inc/
│   └── customizer.php     # 44 Customizer settings (all homepage content)
└── assets/
    └── js/
        ├── main.js        # Header scroll, animations, product tab filtering
        └── stl-viewer.js  # Three.js STL/OBJ file viewer
```

## Content Separation (Classic Theme Approach)

> **Theme = HOW things look. Customizer + Pages = WHAT is shown.**

Unlike block themes (nimbus/smuggler) that use `<!-- wp:post-content -->` empty shells, this classic PHP theme uses a **hybrid approach**:

### Homepage sections → Customizer fields
Complex PHP sections (Three.js viewer, product loops, SVG icons, star ratings) can't be built in the Block Editor. All text/images are Customizer fields with sensible defaults.

### Sub-pages → `the_content()`
`page.php`, `single.php`, `archive.php`, `404.php` use standard `the_content()`. Customers edit these in the normal WordPress page/post editor.

### Homepage also has `the_content()`
`front-page.php` calls `the_content()` at the bottom. If the customer adds content to the Homepage in the page editor, it appears below all Customizer sections. An admin notice guides them to the Customizer for the main sections.

### Navigation → `wp_nav_menu()` with fallbacks
4 registered menu locations. Fallback callbacks provide default links so the theme works out-of-box before menus are configured.

## Customizer Panels & Fields (44 settings)

| Panel | Section | Key Fields |
|-------|---------|------------|
| — | Redigeringsguide | Instructions for the customer |
| Hero-sektion | Badge, Headings, CTA | `hero_badge`, `hero_heading_1/2`, `hero_btn_primary/secondary` |
| Hero-sektion | Bild & statistik | `hero_bg_image`, `hero_product_image`, `stat_1-3_value/label` |
| Hero-sektion | Flytande kort | `float_top/bottom_label/value` |
| — | Trust bar | `trust_1` through `trust_8` |
| Kategorier | Rubrik + 3 cards | `cat_title`, `cat_1-3_name/count/image/link` |
| 3D Viewer | Rubrik + 3 features | `viewer_title/subtitle`, `vf_1-3_title/desc` |
| Produkter | Rubrik + 8 products | `prod_title`, `prod_1-8_name/price/oldprice/cat/badge/image/link` |
| Så funkar det | Rubrik + 4 steps | `process_title/subtitle`, `step_1-4_title/desc` |
| Galleri | Rubrik + 5 items | `gallery_title/subtitle`, `gallery_1-5_title/desc/image` |
| Recensioner | Rubrik + 3 reviews | `testimonials_label/title`, `review_1-3_text/name/role/avatar` |
| Nyhetsbrev | Rubrik + text | `nl_heading`, `nl_text` |
| Footer | Contact + social | `footer_desc/email/phone/address`, `social_instagram/facebook/pinterest/tiktok` |

Helper functions (defined in `customizer.php`):
- `m('field', 'default')` — get text field, escaped
- `m_url('field', 'default')` — get URL field
- `m_raw('field', 'default')` — get HTML field (wp_kses_post)

## Navigation Menu Locations

| Location | Used In | Fallback Function |
|----------|---------|-------------------|
| `primary` | Desktop header nav | `makiro_primary_nav_fallback()` |
| `mobile` | Mobile slide-out menu | `makiro_mobile_nav_fallback()` |
| `footer_shop` | Footer "Shoppa" column | `makiro_footer_shop_fallback()` |
| `footer_info` | Footer "Information" column | `makiro_footer_info_fallback()` |

## Cart Icon Behavior

The cart icon always shows in the header:
- **With WooCommerce**: Links to cart page, shows item count badge
- **Without WooCommerce**: Links to `#produkter` as fallback, no badge

## Product Cards

Product cards are `<a>` tags (clickable links), not `<div>`s. Each product has a Customizer `prod_N_link` field (defaults to `#kontakt`). On hover, "Snabbvy" and "Köp" action buttons appear.

## Three.js STL Viewer

`assets/js/stl-viewer.js` provides an interactive 3D model viewer:
- Drag & drop STL/OBJ files
- Orbit controls (rotate, zoom, pan)
- Color picker (5 preset colors)
- Wireframe toggle, auto-rotate
- Estimated price display

Dependencies loaded from CDN: `three.js r128`, `OrbitControls`, `STLLoader`.

## Key CSS Patterns

### Design tokens (`:root` in style.css)
```css
--color-bg: #0a0a0a;
--color-accent: #c8ff00;
--color-text: #f5f5f5;
--font-display: 'Space Grotesk';
--font-body: 'Inter';
```

### SVG icons on dark backgrounds
All SVG icons use `stroke="currentColor"`. Parent elements MUST set `color`:
```css
.btn-icon { color: var(--color-text); }
```

### Scroll animations
`.animate-in` elements start at `opacity: 0` + `translateY(20px)`, become `.visible` via IntersectionObserver. Accessibility fallbacks:
- `<noscript>` style in `<head>` forces visibility
- `@media (prefers-reduced-motion: reduce)` disables transitions

## What the Customer Can Do (No Code)

Via **Utseende → Anpassa** (Customizer):
- Edit all homepage text, images, product info, reviews
- Update contact info and social links
- Change product links and prices

Via **Utseende → Menyer**:
- Add/remove/reorder nav links (header, mobile, footer)

Via **Sidor** (Pages):
- Edit sub-pages (Om Makiro, Kontakt, FAQ, etc.)
- Add new pages with standard block editor

Via **Inlägg** (Posts):
- Create blog posts and news

Via **Media**:
- Upload and replace images

## Testing Checklist

- [ ] Cart icon visible in header (light on dark background)
- [ ] Product cards are clickable links
- [ ] Product tab filtering works (shows/hides by category)
- [ ] Mobile menu opens/closes
- [ ] Scroll animations trigger (elements fade in on scroll)
- [ ] Animations respect `prefers-reduced-motion`
- [ ] STL viewer accepts file upload and renders model
- [ ] All Customizer fields render on homepage
- [ ] Sub-pages (page.php) show `the_content()`
- [ ] Footer nav menus render (or fallbacks work)
- [ ] No broken images at default state
- [ ] Responsive at 375px, 768px, 1024px, 1440px

## Verification

```bash
bash scripts/verify-site.sh                       # Local
bash scripts/verify-site.sh https://example.com    # Production
python3 scripts/screenshot.py --segments           # Visual screenshots with scroll captures
```
