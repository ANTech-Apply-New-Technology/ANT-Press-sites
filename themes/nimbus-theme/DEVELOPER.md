# Nimbus Theme — Developer Guide

Reusable WordPress block theme template inspired by [nimbusgroup.se](https://www.nimbusgroup.se/sv/). Fork this for new client projects that need a premium, corporate Scandinavian aesthetic.

## Design Language

- **Typography**: Georgia serif italic for headings, system sans-serif for body
- **Colors**: Dark navy (#1C2333) + white only. No accent color. Restrained.
- **Buttons**: Outline/ghost style only — no filled buttons
- **Layout**: Edge-to-edge alternating sections (image left/right), extreme whitespace
- **Header**: Always hamburger menu (even desktop) — no visible nav links
- **Menu overlay**: Fullscreen dark navy with multi-column links (desktop), accordion (mobile)

## Architecture

```
nimbus-theme/
├── style.css              # Design tokens (:root vars) + all component CSS
├── theme.json             # Block theme config (colors, fonts, spacing, block defaults)
├── functions.php          # Menu overlay (wp_footer), wp_nav_menu(), Customizer fields, SEO
├── parts/
│   ├── header.html        # Custom HTML header (logo center + Meny button)
│   └── footer.html        # 5-column dark footer
├── templates/
│   ├── front-page.html    # Homepage (empty shell: header + wp:post-content + footer)
│   ├── page.html          # Generic page (empty shell)
│   ├── page-*.html        # Slug-specific page templates (empty shells)
│   ├── single.html        # Blog post
│   ├── archive.html       # Post archive
│   ├── index.html         # Fallback
│   └── 404.html           # Not found
├── patterns/
│   ├── hero-fullwidth.php # Full-viewport cover hero
│   ├── about-split.php    # 50/50 image + text
│   ├── boats-grid.php     # Alternating edge-to-edge product rows
│   ├── stats-row.php      # Number statistics row
│   ├── racing-showcase.php # Dark section with card grid
│   ├── services-grid.php  # Service cards with thin top border
│   ├── news-feed.php      # WP Query: date + title list (Pressmeddelanden)
│   ├── partners-logos.php # Partner name row
│   ├── cta-section.php    # Two-column CTA (dark navy)
│   └── mission-statement.php # Centered italic quote
├── assets/
│   ├── css/custom.css     # Animations, scroll effects, calculator
│   ├── js/
│   │   ├── header-scroll.js    # Header transparency + menu overlay toggle
│   │   ├── scroll-animations.js # Fade-in on scroll
│   │   ├── contact-protect.js   # Email/phone obfuscation
│   │   └── price-calculator.js  # Service price calculator
│   └── images/            # Theme images (boats, services, hero)
└── screenshot.png         # Theme preview (1200x900)
```

## How the Header + Menu Works

This is the most complex part of the theme. Four files coordinate:

### 1. `parts/header.html` — Structure
Custom HTML wrapped in `<!-- wp:html -->`. Contains:
- Center logo (`.smuggler-logo`) — text link, not image
- Right-side menu button (`.smuggler-menu-toggle`) with "Meny" text + 3x3 dot grid

### 2. `functions.php` — Menu overlay HTML
`smuggler_menu_overlay()` hooked to `wp_footer` outputs the overlay `<div>`:
- Desktop: 3-column grid (`.smuggler-menu-columns`) with headings + link lists
- Mobile: Accordion (`.smuggler-menu-mobile`) with expandable sections
- Bottom CTA row (`.smuggler-menu-bottom`)

**To customize menu links**: Create menus in Appearance > Menus and assign to the 4 registered locations. Or edit fallback callbacks in `smuggler_menu_overlay()` for the out-of-box experience.

### 3. `assets/js/header-scroll.js` — Behavior
Handles three things:
- **Scroll state**: Toggles `.is-transparent` / `.is-solid` on the header based on scroll position
- **Dark hero detection**: Checks if first content block is a cover/dark section
- **Menu overlay**: Open/close toggle, Escape key, link auto-close, accordion expand/collapse

### 4. `style.css` — Visual states
Three header color states, each affecting logo, text, and dot colors:
- `.is-transparent` — white text (on dark hero)
- `.is-solid` — dark navy text (scrolled or light pages)
- `.is-menu-open` — white text (on dark overlay)

The overlay itself: `.smuggler-menu-overlay` (fixed, full viewport, z-index 999).

## Forking for a New Project

1. Copy `nimbus-theme/` → `wp-content/themes/your-client-theme/`
2. Update `style.css` header (Theme Name, Author, Text Domain, Description)
3. Search & replace `smuggler` → `yourclient` in:
   - `functions.php` (function names, text domain)
   - `style.css` (CSS class names — or keep `smuggler-` prefix, it works)
   - `theme.json` (text domain reference)
4. Update `:root` CSS variables in `style.css` for client colors
5. Replace placeholder images in `assets/images/`
6. Update Customizer defaults in `functions.php` (company name, address, etc.)
7. Update fallback menu callbacks in `functions.php` with client nav links
8. Edit footer columns in `parts/footer.html` (editable via Site Editor too)
9. Update patterns with client placeholder text
10. Run `bash scripts/seed-content.sh` to create initial WordPress pages
11. Run `bash scripts/verify-site.sh` to validate everything

## CSS Custom Properties (Design Tokens)

All in `:root` at the top of `style.css`:

```css
--smuggler-primary: #1C2333;     /* Dark navy — header, text, backgrounds */
--smuggler-secondary: #3D6B99;   /* Blue accent (hover links only) */
--smuggler-bg: #FFFFFF;          /* Page background */
--smuggler-surface: #F5F5F3;     /* Alternating section bg */
--smuggler-text: #1A1A2E;        /* Body text */
--smuggler-muted: #6B7280;       /* Secondary text */
--smuggler-border: #E5E7EB;      /* Dividers */
--smuggler-white: #FFFFFF;       /* White (for dark sections) */
--smuggler-transition: 0.3s ease; /* Global transition */
```

## Key CSS Patterns

### Edge-to-edge alternating rows
```css
.nimbus-alt-row { /* applied to wp:columns */ }
/* Columns with 0 gap, 50/50, full-width images */
```

### White outline buttons on dark backgrounds
Selector chain: `.has-primary-background-color`, `.has-racing-dark-background-color`, `.wp-block-cover`, `[style*="background-color:#2A3A52"]`

### Menu overlay desktop columns left-aligned
```css
.smuggler-menu-overlay-inner {
    padding-right: 30%;  /* pushes content left */
}
```

## Content Separation Architecture (v3.0)

> **Theme = HOW things look. Database = WHAT is shown.**
> An end-customer should NEVER need to open a code file to update text, images, prices, or contact info.

Full rules: `design-system/CONTENT-SEPARATION-RULES.md`

### Templates are empty shells
Every template (`front-page.html`, `page-*.html`) contains only:
```html
<!-- wp:template-part {"slug":"header","tagName":"header"} /-->
<!-- wp:post-content {"align":"full","layout":{"type":"default"}} /-->
<!-- wp:template-part {"slug":"footer","tagName":"footer"} /-->
```
ALL text, images, and sections live in the WordPress page editor (database).

### Patterns are insertable, not hardcoded
Patterns are registered in `functions.php` and available via Block Editor "+" > Patterns. They contain placeholder text the customer replaces. They are NOT referenced in templates — the customer inserts them into pages via the editor.

### Navigation uses `wp_nav_menu()`
`functions.php` registers 4 menu locations:
- `overlay_boats` — Boats column (desktop)
- `overlay_company` — Company column (desktop)
- `overlay_contact` — Contact column (desktop)
- `overlay_mobile` — Mobile accordion

Menus are editable via Appearance > Menus. Hardcoded fallback callbacks ensure the menu works out of the box before the customer configures menus.

### Company info in Customizer
`functions.php` registers a "Företagsinformation" Customizer section with:

| Field | Key | Default |
|-------|-----|---------|
| Company name | `company_name` | Smugglerbåtar AB |
| Address | `company_address` | Kråkviksv. 8, 761 94 Norrtälje |
| Phone | `company_phone` | (empty) |
| Email | `company_email` | (empty) |
| Org.nr | `company_org_nr` | (empty) |
| Founded year | `company_founded` | 2001 |
| Facebook | `social_facebook` | (empty) |
| Instagram | `social_instagram` | (empty) |
| LinkedIn | `social_linkedin` | (empty) |

Use `smuggler_company('field_name', 'fallback')` in PHP to retrieve values.

### Seed script for initial content
```bash
bash scripts/seed-content.sh          # Create pages + set front page
bash scripts/seed-content.sh --check  # Audit which pages exist
bash scripts/seed-content.sh --reset  # Delete + recreate all pages
```
Creates 7 pages (Hem, Båtar, Tjänster, Racing, Kontakt, Om oss, Produkter) with pattern references in the page content.

### What the customer can do (no code)
- **Pages**: Edit all text, images, buttons; reorder sections; add new sections from patterns
- **Menus**: Add/remove/reorder navigation links (Appearance > Menus)
- **Company info**: Update name, address, phone, email, social links (Appearance > Customize)
- **Posts**: Create news articles and blog posts
- **Media**: Upload and replace images

## Gotchas

- **Header uses `<!-- wp:html -->`** — not standard block markup. Site Editor can edit it but shows raw HTML.
- **Menu overlay is PHP** — edit `functions.php`, not a template part. Can't be edited from Site Editor.
- **Custom header JS** — the IIFE in `header-scroll.js` returns early if `.smuggler-header` doesn't exist. If you rename the class, update both CSS and JS.
- **Dark hero detection** — JS checks for `.smuggler-hero`, `.wp-block-cover`, `.has-primary-background-color`, or `.has-racing-dark-background-color` as the first content after header. If none found, header stays solid.
- **Mobile overlay is also dark navy** — not white. Same background color on all viewports.
- **CSS Grid overflow**: NEVER put `overflow: hidden` on text containers. Use `:has(> figure)` to target only image columns. Text columns need `min-width: 0` + `overflow-wrap: break-word` + `padding: clamp(...)`.
- **Alternating row order**: When chaining patterns (about-split → boats-grid), the first row of the new pattern must be the OPPOSITE layout of the last row of the previous pattern.
- **Swedish ÅÄÖ**: AI-generated Swedish text drops special characters. Run `bash scripts/verify-site.sh` (Test 9) to auto-detect and flag ASCII substitutes.

## Testing Checklist

- [ ] Header transparent on hero, solid on scroll
- [ ] Menu opens/closes on click
- [ ] Menu closes on Escape key
- [ ] Menu closes when clicking a link
- [ ] Mobile accordion expands/collapses (only one open at a time)
- [ ] Outline buttons visible on all dark backgrounds
- [ ] Images load in boats grid
- [ ] News query shows recent posts
- [ ] Footer renders 5 columns on desktop, stacks on mobile
- [ ] No orange/accent colors anywhere (Nimbus = navy + white only)
- [ ] All page content editable via wp-admin Pages editor (not empty)
- [ ] Alternating rows alternate correctly across pattern boundaries
- [ ] No text overflow/clipping at 375px, 768px, 1024px, 1440px viewports
- [ ] Swedish ÅÄÖ characters present (run `bash scripts/verify-site.sh`)
- [ ] Customizer fields populated (Appearance > Customize > Företagsinformation)

## Verification Suite

Run the full test suite before any deployment:
```bash
bash scripts/verify-site.sh                       # Local
bash scripts/verify-site.sh https://example.com    # Production
```

Tests include: HTTP checks, REST API, navigation elements, static assets, headers, visual screenshots (Playwright), content separation audit, and Swedish language check.
