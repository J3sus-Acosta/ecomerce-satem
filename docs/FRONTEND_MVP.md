# SATEM E-COMMERCE FRONTEND MVP SPECIFICATION & REBRANDING MANUAL

## 1. FRONTEND ARCHITECTURE OVERVIEW

The frontend of **SATEM Soluciones E-Commerce** is built using a **Theme-Driven Reusable Architecture** implemented in the child theme `themes/satem-child/`.

```text
[ CLIENT BROWSER / DEVICE ]
            │
            ▼
[ WORDPRESS CHILD THEME (`satem-child`) ]
    ├── System Design Tokens (`style.css` :root variables)
    ├── Semantic Templates (`header.php`, `footer.php`, `front-page.php`, `page.php`, `template-wholesale.php`, `woocommerce.php`)
    ├── Asset Logic (`functions.php` & `assets/js/satem-frontend.js`)
            │
            ▼
[ BUSINESS ENGINE (`satem-core`) & WOOCOMMERCE ]
    ├── Role Pricing (`b2b_toko`, `b2b_restaurant`, `b2b_supermarket`)
    ├── Case Pack Multiples (`_satem_units_per_box`)
    └── Commercial Registration Shortcode (`[satem_b2b_registration_form]`)
```

### Architectural Principles
1. **Total Prohibition of Page Builders (ADR-014)**: Developed 100% natively using semantic HTML5, CSS custom properties, vanilla JS, and WooCommerce template hooks.
2. **Reusability & Portability (ADR-022)**: Designed so the entire MVP can be transformed into the definitive client storefront or reused for future clients simply by updating visual tokens and database records.
3. **Decoupled Business Logic**: Presentation remains strictly inside `satem-child`, while business logic, pricing hooks, and box multiples remain encapsulated inside `satem-core`.

---

## 2. DESIGN SYSTEM TOKENS (`style.css`)

All visual styling, brand colors, typography, layout dimensions, and shadows are defined in a centralized token map within `themes/satem-child/style.css`:

```css
:root {
	/* Primary Brand Palette */
	--satem-primary: #0F172A;
	--satem-primary-dark: #020617;
	--satem-secondary: #1E293B;
	--satem-accent: #2563EB;
	--satem-accent-hover: #1D4ED8;

	/* Surfaces & Backgrounds */
	--satem-bg-main: #F8FAFC;
	--satem-surface: #FFFFFF;
	--satem-surface-subtle: #F1F5F9;
	--satem-border: #E2E8F0;

	/* Text Colors */
	--satem-text: #334155;
	--satem-text-heading: #0F172A;
	--satem-text-muted: #64748B;

	/* Badges & Status */
	--satem-success: #16A34A;
	--satem-warning: #D97706;
	--satem-danger: #DC2626;
	--satem-badge-b2b-bg: #EFF6FF;
	--satem-badge-b2b-text: #1D4ED8;

	/* Layout & Radius */
	--satem-container-max: 1280px;
	--satem-radius-sm: 6px;
	--satem-radius-md: 10px;
	--satem-radius-lg: 16px;

	/* Typography */
	--satem-font-base: 'Inter', system-ui, sans-serif;
	--satem-font-heading: 'Outfit', 'Inter', system-ui, sans-serif;
}
```

---

## 3. IMPLEMENTED PAGES & LAYOUT COMPONENT INVENTORY

| Page / Component | File Location | Key Features & Responsibilities |
|---|---|---|
| **Header & Navigation** | `header.php` | Sticky elevation, brand logo area, horizontal desktop menu, dynamic B2B badge link, cart item count pill, mobile hamburger toggle. |
| **Footer** | `footer.php` | SATEM slogan ("Si no Existe, Lo Creamos..."), quick navigation columns, B2B links, legal info placeholders, copyright. |
| **Commercial Home** | `front-page.php` | Technology Hero banner, "Shop by Category" visual cards, "Featured Products" grid, B2B Wholesale callout banner. |
| **Wholesale B2B Landing** | `template-wholesale.php` | Commercial account application layout embedding `[satem_b2b_registration_form]`. |
| **Shop & Archives** | `woocommerce.php` | Custom catalog title, search & category filter header, 4-column responsive grid, sorting dropdown. |
| **Product Card** | `functions.php` + `style.css` | Image ratio, product title, public or wholesale price tag, B2B badge, Add to Cart button. |
| **Single Product Detail** | `functions.php` hooks | Product gallery, pricing, stock status, Case Pack details box (`Case Pack: 12 units`), quantity stepping helper. |
| **Cart & Checkout** | `style.css` | Styled WooCommerce Blocks integration matching tokens. |
| **My Account** | `functions.php` + `style.css` | Standard navigation plus B2B Wholesale Account status box showing approval status and business channel (`Toko`, `Restaurant`, `Supermarket`). |

---

## 4. B2C VS B2B FRONTEND EXPERIENCE INTEGRATION

### B2C (Retail Buyers & Guests)
- **Prices**: Displays standard public retail price (`_regular_price`).
- **Quantity Selector**: Single unit step (1 by 1) with no purchase minimums.
- **Security & Privacy**: Zero wholesale pricing or box packaging internal metadata exposed.

### B2B (Approved Commercial Buyers: `b2b_toko`, `b2b_restaurant`, `b2b_supermarket`)
- **Prices**: Dynamically filtered in memory by `satem-core` (`Satem_B2B_Pricing`). Displays wholesale price tag with `Wholesale Price` badge.
- **Packaging Rules**: Displays `Case Pack Specifications` box when `_satem_units_per_box > 1`. Quantity selector defaults to minimum and steps in multiples of case units.
- **Header & Account Badges**: Displays `Wholesale Account` indicator in header navigation and My Account dashboard.

---

## 5. RESPONSIVE BREAKPOINT SPECIFICATIONS

Layouts have been engineered and tested across standard device viewports:
- **Mobile Extra Small (360px, 390px, 414px)**: Single column product grid, collapsed hamburger navigation drawer, full-width buttons.
- **Tablet (768px - 1024px)**: 2 to 3 column product grid, responsive flex hero layout, multi-row footer grid.
- **Desktop (1280px, 1440px, 1920px)**: 4 column product grid, max container width 1280px, horizontal header navigation bar.

---

## 6. CLIENT REBRANDING & GO-LIVE CUSTOMIZATION GUIDE

To convert this MVP into the definitive client store or adapt it for a new client:

### 1. Visual Identity & Branding
- **Logo**: Update `.satem-logo-area` in `header.php` or replace text with an `<img>` tag pointing to the client's official SVG/PNG logo.
- **Colors**: Modify `:root` CSS variables in `themes/satem-child/style.css` (`--satem-accent`, `--satem-primary`, `--satem-bg-main`).
- **Fonts**: Replace Google Font import in `themes/satem-child/functions.php` and update `--satem-font-base` / `--satem-font-heading`.

### 2. Commercial Content & Legal Text
- **Hero & Home Titles**: Update hero title and descriptions in `themes/satem-child/front-page.php`.
- **Footer Contact & Legal Info**: Update address, email, phone numbers, and legal links in `themes/satem-child/footer.php`.

### 3. Product Catalog & Categories
- **Import Real Catalog**: Upload product catalog via WooCommerce CSV import using `catalog-template.csv`.
- **Set Box Multiples & Barcodes**: Set `_satem_units_per_box`, `_satem_barcode_unit`, `_satem_barcode_box` on imported products.
- **Set B2B Prices**: Set wholesale prices (`_satem_price_b2b_toko`, `_satem_price_b2b_restaurant`, `_satem_price_b2b_supermarket`) in product admin screens.

### 4. Commercial Configurations (WooCommerce Settings)
- **Taxes**: Configure Curaçao sales tax (OB) under `WooCommerce -> Settings -> Tax`.
- **Payment Gateways**: Install and enable local payment gateways (CX Pay, FAC, BACS) under `WooCommerce -> Settings -> Payments`.
- **Shipping Rates**: Configure delivery zones and rates under `WooCommerce -> Settings -> Shipping`.

---

## 7. PENDING CLIENT DATA INVENTORY

| Category | Item Description | Current MVP State | Action Required Upon Client Delivery |
|---|---|---|---|
| **Branding** | Vector Logo & Brand Guidelines | SATEM Provisional Identity | Replace logo file and CSS color tokens |
| **Catalog** | Full Product Excel/CSV Matrix | Demo Test Products | Run WooCommerce CSV Importer |
| **B2B Pricing** | Wholesale Price List per Channel | Demo Test Prices | Fill `_satem_price_b2b_*` meta fields |
| **Taxes** | Official OB Tax Percentage | 0% (Disabled) | Enable WC Taxes & set rate percentage |
| **Payment** | Gateway Provider & Credentials | Disabled | Configure CX Pay / FAC / BACS |
| **Shipping** | Delivery Zones & Minimum Amounts | Pending Rules | Configure WC Shipping Zones |
| **Legal** | Privacy & Terms Content | Placeholder Links | Create WP Pages with official legal texts |
