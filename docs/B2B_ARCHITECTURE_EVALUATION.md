# TECHNICAL EVALUATION & ARCHITECTURE DESIGN FOR CUSTOM B2B ENGINE — SATEM E-COMMERCE

This document presents a comprehensive technical analysis evaluating whether **SATEM Soluciones** should implement a proprietary, lightweight B2B engine directly inside `satem-core` rather than relying on third-party commercial plugins (such as B2BKing Pro).

---

## 1. BUSINESS & FUNCTIONAL REQUIREMENTS

The store requires a hybrid retail/wholesale model for Curaçao:

### B2C Channel (Retail Public)
- Guest checkout enabled (no registration mandatory).
- Purchase per single unit.
- Standard retail list price (`regular_price`).

### B2B Channel (Wholesale Commercial)
- **Target Groups**: Tokos (`b2b_toko`), Restaurants (`b2b_restaurant`), Supermarkets (`b2b_supermarket`).
- **Registration & Admin Approval**: Mandatory commercial account registration (collecting CRIB, KVK) with manual admin approval status (`pending_b2b`, `approved_b2b`, `rejected_b2b`).
- **Group Price Matrix**: Role-based pricing per product (e.g. Retail $10.00, Toko $9.00, Restaurant $8.50, Supermarket $8.00).
- **Individual Customer Pricing**: Ability to override specific prices for individual user accounts (e.g. Customer A -> $8.25).
- **Box Quantities & MOQ (Step / Minimums)**: Enforce buying in box multiples using `_satem_units_per_box` (e.g. Min 12, Step 12).
- **Price Visibility**: Hide wholesale pricing from guests and unapproved users.

---

## 2. PROPOSED ARCHITECTURE (`satem-core` vs `satem-b2b`)

### Architecture Options Analyzed:
- **Option A: Modular Engine inside `plugins/satem-core/` (RECOMENDADA)**
  - All B2B logic is organized in a decoupled module (`plugins/satem-core/includes/class-satem-b2b.php`).
  - *Advantages*: Single repository plugin, zero multi-plugin overhead, direct shared access to packaging metadata (`_satem_units_per_box`, barcodes) for picking REST API endpoints.
- **Option B: Independent Plugin `plugins/satem-b2b/`**
  - *Advantages*: Clean separation of concerns.
  - *Disadvantages*: Requires duplicate HPOS compatibility declarations, inter-plugin hook dependency management.

**Architectural Recommendation**: **Option A**. Keep B2B logic cleanly encapsulated inside `satem-core` as a dedicated modular subsystem (`Satem_B2B`).

---

## 3. DATA MODEL & STORAGE SPECIFICATIONS

The custom B2B engine requires zero custom database tables, utilizing native WordPress & WooCommerce metadata structures for 100% HPOS and WP-CLI compatibility:

```text
[ WordPress User ]
  ├── User Role: b2b_toko | b2b_restaurant | b2b_supermarket
  └── User Meta:
        ├── _satem_b2b_approval_status: 'pending_b2b' | 'approved_b2b' | 'rejected_b2b'
        ├── _satem_crib_number: '123456789'
        └── _satem_kvk_number: '98765'

[ WooCommerce Product ]
  ├── Product Meta (Group Prices):
  │     ├── _satem_price_b2b_toko
  │     ├── _satem_price_b2b_restaurant
  │     └── _satem_price_b2b_supermarket
  └── Product Meta (Individual Overrides):
        └── _satem_custom_user_prices: Array( user_id => custom_price )
```

---

## 4. PRICE FILTERING & QUANTITY HOOK MECHANISM

Price overrides and box quantity enforcement are handled purely via native WooCommerce hooks in memory—never mutating database product records:

1. **Dynamic Price Override Filter**:
   ```php
   add_filter( 'woocommerce_product_get_price', 'satem_apply_b2b_price_override', 10, 2 );
   add_filter( 'woocommerce_product_get_regular_price', 'satem_apply_b2b_price_override', 10, 2 );
   ```
   *Logic*: If current user has `approved_b2b` status, check individual user meta override first; if none exists, return group price meta; otherwise fallback to standard retail price.

2. **Box Quantity & Step Enforcement**:
   ```php
   add_filter( 'woocommerce_quantity_input_args', 'satem_apply_b2b_quantity_step', 10, 2 );
   add_filter( 'woocommerce_add_to_cart_validation', 'satem_validate_b2b_cart_quantity', 10, 5 );
   ```
   *Logic*: Automatically sets `step` and `min_quantity` to `_satem_units_per_box` when an approved B2B user views or adds a product to cart.

---

## 5. PERFORMANCE & CACHING STRATEGY

- **Zero SQL Table Joins**: All user role and meta checks leverage `get_user_meta()` and `get_post_meta()`, which are cached in memory by WordPress Object Cache.
- **No Product Loops**: Prices are evaluated lazily on-the-fly per displayed item during page render or REST request.
- **HPOS Compatibility**: 100% compliant with WooCommerce High-Performance Order Storage (HPOS).

---

## 6. COMPARISON MATRIX: B2BKING PRO VS SATEM CUSTOM B2B ENGINE

| Feature / Criteria | B2BKing Pro | SATEM Custom B2B Engine |
|---|---|---|
| **B2B Customer Groups** | Custom Post Types (`b2bking_group`) | Native WP User Roles (`b2b_toko`, etc.) |
| **Tiered Group Pricing** | Meta tables / Complex CPT rules | Post Meta (`_satem_price_b2b_*`) |
| **Individual User Pricing** | Premium Feature | User Meta / Product Meta Array |
| **Box Quantities (Step/MOQ)** | Dynamic Rules Engine | Native Hook (`woocommerce_quantity_input_args`) |
| **B2B Registration & Approval**| Custom Form Builder | Native WP/WC Form + Approval Meta |
| **Performance Impact** | Medium (Heavy CPT queries) | **Ultra High (Direct object cache lookup)** |
| **External Dependencies** | Third-party plugin (KingsPlugins) | **Zero external dependencies** |
| **Source Code Control** | Vendor closed-source zip | **100% Owned in local Git repository** |
| **License Cost** | ~$139 USD / client | **$0 USD (Zero recurring license cost)** |
| **Warehouse Picking Integration** | Indirect | **Direct native REST integration in `satem-core`** |

---

## 7. COMPLEXITY ESTIMATION & RISKS

- **Complexity Level**: **Medium-Low**. The custom engine requires approximately 350-500 lines of clean, structured PHP code across 3 class files.
- **Development Time**: Estimated 8-12 hours of core development.
- **Main Technical Risks**:
  - Mini-cart AJAX caching requiring explicit fragment refreshes.
  - Ensuring guest users never see wholesale pricing accidentally.

---

## 8. FINAL ARCHITECTURAL RECOMMENDATION

**Recommendation: Build the Proprietary SATEM Custom B2B Engine inside `satem-core`**.

### Justification:
1. **Financial Independence**: Saves $139+ USD per site/client and eliminates third-party renewal licenses forever.
2. **Superior Performance**: Eliminates the heavy overhead of B2BKing's Custom Post Types, relying purely on native WordPress user roles and cached metadata.
3. **Full Control & Seamless Integration**: Integrates directly with `_satem_units_per_box` and the future warehouse barcode picking scanner without conflict.
