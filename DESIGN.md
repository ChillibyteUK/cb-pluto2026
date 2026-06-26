# cb-pluto2026 — Theme Design Document

## Overview

**cb-pluto2026** is a WordPress child theme of [Understrap](https://github.com/understrap/understrap) (Bootstrap 5 parent framework), built for [Pluto Finance](https://pluto-finance.com/). It is developed by ChillibyteUK.

- Theme URI: <https://pluto-finance.com/>
- Author: ChillibyteUK — DS
- Version: `0.1.1` (style.css) / `1.2.0` (package.json)
- Text Domain: `cb-pluto2026`
- License: GPL-3.0
- GitHub: <https://github.com/ChillibyteUK/cb-pluto2026>

## Site Architecture — Dual Context

The marketing site is split into two sections sharing most content (posts, portfolio items) but with different navigation, colour variants, CTAs, and footer menus:

| Section | URL prefix | Context constant | Nav menu | Footer menu |
|---|---|---|---|---|
| Property Finance (Lending) | `/property-finance/` | `pf` | `pf_nav` | `footer_menu_lending` |
| Investors | `/investors/` | `inv` | `inv_nav` | `footer_menu_investing` |

Context is sniffed from `$_SERVER['REQUEST_URI']` via `cb_get_site_context()`. Blocks and templates use this to flip CSS modifier classes and URLs.

## Theme Structure

```
cb-pluto2026/
├── acf-json/                  # ACF field group definitions (auto-sync)
├── blocks/                    # ACF block render templates (26 blocks)
├── css/                       # Compiled/minified CSS output
│   ├── child-theme.css
│   ├── child-theme.min.css
│   └── custom-editor-style.min.css
├── fonts/                     # Montserrat (local self-hosted woff2)
├── img/                       # Theme images, logos, SVG icons
├── inc/                       # PHP includes (loaded by functions.php)
│   ├── cb-acf-theme-palette.php   # ACF colour picker palette integration
│   ├── cb-block-usage.php         # Debug shortcode: which blocks used where
│   ├── cb-blocks.php              # Block registration + core block overrides
│   ├── cb-people-contact.php      # Gravity Forms person-contact routing
│   ├── cb-people-import.php       # CSV import for People CPT
│   ├── cb-posttypes.php           # Custom post types (person, portfolio)
│   ├── cb-taxonomies.php          # Custom taxonomies (team, portfolio_solution)
│   ├── cb-theme.php               # Main theme setup (context, menus, widgets, enqueues)
│   ├── cb-utility.php             # Utility functions, shortcodes, SVG helper
│   └── editor-color-palette.json  # Legacy BS colour map
├── js/                        # Compiled + custom JS
├── page-templates/            # Custom page templates
│   ├── home-page.php          # Home Page (full-width hero, video support, two-card layout)
│   └── text-page.php          # Text Page (simple content page)
├── reference-parts/           # Style guide reference templates
├── src/
│   ├── build/                 # Build tool configuration
│   ├── js/                    # Source JavaScript (Bootstrap, custom)
│   ├── phpstan/               # PHPStan autoloader
│   └── sass/
│       ├── child-theme.scss   # Main entry point
│       ├── custom-editor-style.scss
│       ├── assets/            # Bootstrap, Understrap, Font Awesome sources
│       └── theme/             # Custom SCSS
│           ├── _tokens.scss   # Design tokens (colours, typography, spacing)
│           ├── _child_theme_variables.scss
│           ├── _colours.scss
│           ├── _typography.scss
│           ├── _buttons.scss
│           ├── _header.scss
│           ├── _footer.scss
│           ├── blocks/        # Per-block SCSS (one file per block)
│           └── templates/     # Page template SCSS
├── functions.php              # Child theme bootstrap, enqueues, AJAX
├── style.css                  # Theme header
├── theme.json                 # WordPress theme.json (auto-generated from _tokens.scss)
├── generate-theme-json.js     # Script to auto-generate theme.json from SCSS tokens
├── add_block.sh               # Interactive block scaffolder
├── rm_block.sh                # Interactive block remover
├── cleanup_blocks.sh          # Orphaned block cleanup
└── populate_acf_from_block.sh # ACF field sync from blocks
```

## Design Tokens

Design tokens live in `src/sass/theme/_tokens.scss` as CSS custom properties on `:root`. A Node.js script (`generate-theme-json.js`) parses them and auto-generates `theme.json` for the WordPress block editor.

### Colour Palette

| Family | Range | Usage |
|---|---|---|
| Green Dark | 100–1300 | Primary brand (lending section accent) |
| Green Teal | 100–1000 | Secondary brand (investors section accent) |
| Green Mid | 1000 | Mid-green accent |
| Green Light | 1000 | Light-green accent |
| Orange | 1000 | CTA / highlight accent |
| Greys | 400–900 | Text, borders, backgrounds |
| White / Black | — | Base colours |

### Typography

- **Font family:** Montserrat (self-hosted woff2, weights 400/500/600)
- **Scale:** 100 (0.67rem) through 1000 (3.56rem), using numbered tokens
- **Line heights:** Unitless, 100–800 scale
- **Letter spacing:** `--ls-0` through `--ls-40`
- **Font weights:** 400 (regular), 500 (medium), 600 (semibold)

### Layout

- `contentSize`: 1360px
- `wideSize`: 1600px
- Block gap: 28px
- Header height: `--h-top: 62px` (mobile), `--h-top-desktop: 90px`

## Custom Post Types

### Person (`person`)
- Non-public, non-queryable (admin only)
- Supports: title, editor, thumbnail, page-attributes
- Menu icon: `dashicons-nametag`
- Used for team member profiles

### Portfolio (`portfolio`)
- Public, shared across both site contexts
- Supports: title, thumbnail, revisions, author
- Rewrite slug: `/property-finance/portfolio` (with contextual filter)
- Block editor disabled — edited via ACF fields
- Menu icon: `dashicons-chart-line`

## Custom Taxonomies

### Team (`team`)
- Attached to: `person`
- Hierarchical, non-public, flat-like editorial taxonomy
- Terms: leadership, lending-and-credit, investor-relations, etc.

### Portfolio Solution (`portfolio_solution`)
- Attached to: `portfolio`
- Hierarchical, non-public
- For categorising portfolio items by solution type

## ACF Blocks

26 blocks registered in `inc/cb-blocks.php`. Each block follows the same pattern:

1. **PHP template** — `blocks/cb-{name}.php`
2. **SCSS partial** — `src/sass/theme/blocks/_cb_{name}.scss`
3. **ACF JSON** — `acf-json/group_cb_{name}.json`
4. **Registration** — `inc/cb-blocks.php` via `acf_register_block_type()`
5. **Import** — `src/sass/theme/blocks/_blocks.scss`

### Block List

| Block | Description |
|---|---|
| cb-accordion-tabs | Accordion / tabs component |
| cb-animated-map | Animated map display |
| cb-bg-text-repeater | Background text repeater |
| cb-card-grid | Card grid (2 or 3 col) with card/image layouts |
| cb-case-studies | Case studies display |
| cb-contact-full | Full contact section |
| cb-contact-map | Contact with map |
| cb-definition-cards | Definition card display |
| cb-feature-title | Feature title block |
| cb-image-cta | Image CTA with parallax background |
| cb-insights-index | Insights listing with AJAX filtering |
| cb-latest-posts | Latest posts grid |
| cb-markets-map | Markets map |
| cb-nav-cards | Navigation cards |
| cb-portfolio-index | Portfolio listing |
| cb-quote | Pull quote with author image/signature |
| cb-secondary-hero | Secondary hero with parallax background |
| cb-show-hide-cards | Show/hide card component |
| cb-team | Team member grid |
| cb-team-simple | Simple team listing |
| cb-text-definition | Text with definition styling |
| cb-text-image | Text + image (split layout, full-bleed option) |
| cb-text-pullout | Text pullout |
| cb-ticker-x3 | Ticker / stat display (3 items) |
| cb-topic-home-hero | Topic hero with parallax |
| cb-two-col-checklist | Two-column checklist |

### Block Scaffolding

Use `./add_block.sh` to scaffold a new block. It:
1. Creates `blocks/cb-{name}.php` with boilerplate
2. Creates `src/sass/theme/blocks/_cb_{name}.scss` (empty)
3. Adds `@import` to `_blocks.scss`
4. Registers the block in `inc/cb-blocks.php` at the `// INSERT NEW BLOCKS HERE.` marker
5. Creates `acf-json/group_cb_{name}.json`

Use `./rm_block.sh` for interactive removal.

Flag `-c` on `add_block.sh` adds Gutenberg colour picker support to the boilerplate.

## Build System

### CSS Pipeline
```
src/sass/child-theme.scss
  → sass (expanded, source maps)
  → postcss (autoprefixer, understrap palette generator)
  → cleancss (minified, .min.css)
```

### JS Pipeline
```
src/js/*.js
  → rollup (with babel, commonjs, multi-entry)
  → terser (minified)
```

### Commands

| Command | Description |
|---|---|
| `npm run css` | Full CSS build (compile → postprocess → minify) |
| `npm run js` | Full JS build (compile → minify) |
| `npm run dist` | CSS + JS build |
| `npm run watch` | Watch SCSS/JS and rebuild |
| `npm run bs` | Watch + BrowserSync |
| `npm run format` | Prettier for JS/SCSS/JSON/MD |
| `npm run lint:php` | PHPCS lint |
| `npm run fix:php` | PHPCBF auto-fix |
| `npm run generate-theme-json` | Regenerate theme.json from SCSS tokens |
| `npm run dist-build` | Create distribution build |

## Third-Party Dependencies

| Dependency | Version | Purpose |
|---|---|---|
| Bootstrap | ^5.3.3 | CSS framework (via Understrap) |
| GSAP | 3.12.7 | Animations, ScrollTrigger |
| Swiper | 10 | Sliders/carousels |
| AOS | 2.3.1 | Scroll reveal animations |
| Lenis | 1.3.11 | Smooth scrolling |
| Font Awesome | 4.7.0 / 6 | Icons (SVG-based, FA4 in devDeps) |
| Popper.js | ^2.11.8 | Bootstrap tooltips/popovers |
| Gravity Forms | — | Contact forms (team member routing) |
| Contact Form 7 | — | General forms (with honeypot) |
| Yoast SEO | — | Canonical URL filtering, meta |

## Key Patterns and Conventions

### Naming
- **CSS:** BEM-like with `cb-` prefix: `.cb-block-name__element--modifier`
- **Block slugs:** kebab in filenames (`blocks/cb-text-image.php`), underscore in PHP (`cb_text_image`)
- **Context modifiers:** `cb-{block}--pf` (lending), `cb-{block}--inv` (investors)
- **ACF field keys:** `field_{block_slug}_{field_name}`

### Block Structure
Every block template follows this pattern:
```php
defined('ABSPATH') || exit;
// Get fields.
// Bail early on empty content.
// Build classes and styles.
// Render section with optional inline parallax script.
```

### Parallax Pattern
Blocks with background images use scroll-driven CSS custom properties:
```js
// Inline script sets --{block}-parallax-y via requestAnimationFrame
window.addEventListener('scroll', onScroll, { passive: true });
```
The CSS uses this variable to translate the background.

### Context-Aware Blocks
Blocks call `cb_get_site_context()` and add `--pf` or `--inv` CSS modifier classes to flip colours per section.

### Gutenberg Colour Picker Support
Blocks with `'color' => array('background' => true, 'text' => true)` in registration use `has-{slug}-background-color` / `has-{slug}-color` classes automatically.

### SVG Sanitisation
Utility function `cb_sanitise_svg()` handles SVG rendering with:
- Style namespace isolation (unique suffix per SVG)
- Dimension and class injection on root `<svg>`
- Accessible defaults (aria-hidden, focusable)
- Strict allowlist via `wp_kses()`

### Rewrite Rules
Custom rewrite rules in `cb_posttypes.php` resolve page-vs-CPT ambiguity for contextual URLs:
- `/investors/insights/`, `/property-finance/insights/`
- `/investors/portfolio/`, `/property-finance/portfolio/`

Both insights (core `post` type) and portfolio items use contextual permalink filters (`post_link`, `post_type_link`) and canonical URL overrides (Yoast).

### Gravity Forms Team Contact
The `cb-team` block integrates a GF form modal per person:
- Person ID passed via hidden field
- Notification routed dynamically to the person's email (stored as ACF field, never rendered to page)
- CC sent to site-wide `contact_email`
- Field resolution by admin label, cached with self-healing

## Enqueued Scripts and Styles

### Frontend
| Handle | Source | Notes |
|---|---|---|
| `cb-theme` | `css/child-theme.min.css` | Main stylesheet, no deps (prevents FOUC) |
| `cb-theme-js` | `js/child-theme.min.js` | Bootstrap + custom JS |
| `cb-accordion-tabs` | `js/cb-accordion-tabs.js` | Accordion/tabs UI |
| `cb-team-filter` | `js/cb-team-filter.js` | Team filtering |
| `aos` | unpkg CDN | AOS library |
| `aos-style` | unpkg CDN | AOS styles |
| `swiper` | CDN | Swiper library |
| `lenis` | unpkg CDN | Smooth scroll library |
| `lenis-style` | unpkg CDN | Lenis base styles |
| `gsap` | CDN | GSAP animation library |
| `gsap-scrolltrigger` | CDN | GSAP ScrollTrigger plugin |

### Admin
| Handle | Source | Purpose |
|---|---|---|
| `understrap_child_customizer` | `js/customizer-controls.js` | Customizer warning dialog |
| `cb-sideload-image` | `js/cb-sideload-image.js` | REST endpoint for image sideloading |
| `cb-editor-fix` | `js/cb-editor-fix.js` | ACF repeater WYSIWYG fix |

## Page Templates

### Home Page (`home-page.php`)
- Full-viewport hero with optional video (MP4/WebM) or featured image background
- Parallax scroll effect via inline JS (`--home-page-hero-parallax-y`)
- Two-card navigation to Lending / Investing sections
- Gutenberg editor disabled (ACF-only editing)
- Featured image support retained

### Text Page (`text-page.php`)
- Simple content layout with sidebar-safe column structure
- Standard `the_content()` with `apply_filters`

## 🔧 Formatting Rules

### PHP Control Structures

**Colon syntax (`:` / `endif;` / `endwhile;` / `endforeach;`) MUST be avoided where possible in favour of brace syntax (`{` / `}`).**

```php
// ✅ CORRECT — brace syntax
<?php if ( $condition ) { ?>
  <p>Content</p>
<?php } ?>

// ❌ AVOID — colon syntax
<?php if ( $condition ) : ?>
  <p>Content</p>
<?php endif; ?>
```

Colon syntax is the default generated by many WordPress templates and is found in some existing block templates. When editing or refactoring, convert to brace syntax. New code MUST use brace syntax.

The only exception is when using `get_field()` with short echo tags inside HTML context, where ternary/early-return patterns are preferred:
```php
<?php if ( '' !== trim( $block_title ) ) { ?>
  <h1><?= esc_html( $block_title ); ?></h1>
<?php } ?>
```
