# DETAILED TECHNICAL SPECIFICATION: PROPRIETARY SATEM B2B ENGINE

This document details the architectural specifications, data model, security design, and execution plan for the proprietary **SATEM B2B Engine** to be integrated into `plugins/satem-core/`.

---

## 1. COMPONENT ARCHITECTURE & FILE STRUCTURE

The B2B engine will be organized into clean, single-responsibility PHP classes inside `plugins/satem-core/includes/`:

```text
plugins/satem-core/
├── satem-core.php (Main Plugin Loader & Hook Registry)
└── includes/
    ├── class-satem-hpos.php           # HPOS declaration and compatibility
    ├── class-satem-packaging.php      # Packaging meta fields & REST API (`satem_packaging`)
    ├── class-satem-b2b-roles.php      # WP User Roles (`b2b_toko`, `b2b_restaurant`, `b2b_supermarket`)
    ├── class-satem-b2b-registration.php# Commercial Registration Form, Nonce & Email Notifications
    ├── class-satem-b2b-pricing.php    # Memory Price Filter Engine (B2C vs B2B & User Overrides)
    ├── class-satem-b2b-cart.php       # Server-Side Quantity Step & Cart Validation
    └── class-satem-b2b-admin.php      # Admin User Approval UI & Product Data B2B Pricing Panel
```

---

## 2. PRICING PRECEDENCE & MEMORY OVERRIDE ENGINE

Price calculations operate **strictly in memory** via WooCommerce price hooks. Database product prices (`_regular_price`, `_sale_price`) are NEVER mutated.

### Pricing Precedence Order:
1. **Individual User Override** (`_satem_custom_user_prices` array on product or user meta) -> **Highest Priority**.
2. **B2B Group Role Price** (`_satem_price_b2b_toko`, `_satem_price_b2b_restaurant`, `_satem_price_b2b_supermarket`) -> **Second Priority**.
3. **Regular Retail Price** (`_regular_price`) -> **Default Fallback (B2C & Unapproved B2B)**.

### Target Hooks (PHP & Store API / Blocks):
- `woocommerce_product_get_price` & `woocommerce_product_get_regular_price` (Product displays & PHP templates).
- `woocommerce_product_variation_get_price` (Variable products if used).
- `woocommerce_cart_item_price` & `woocommerce_cart_item_subtotal` (Cart & Checkout).
- Store API Filter: `woocommerce_store_api_register_update_callback` (WooCommerce Blocks compatibility).

---

## 3. B2B ACCOUNT APPROVAL WORKFLOW

```text
Guest Visitor
   │
   ▼
Submits B2B Registration Form (CRIB, KVK, Company Name) [Nonce Protected]
   │
   ▼
Account Created -> Status: `_satem_b2b_approval_status = 'pending_b2b'`
   │
   ▼
Admin / Shop Manager Notification
   │
   ▼
Admin Reviews in WP Admin -> Users -> B2B Approvals Panel
   │
   ├── Approve -> Status: `approved_b2b` + Role Assigned (`b2b_toko` / `b2b_restaurant` / `b2b_supermarket`)
   │              └─> Confirmation Email Sent -> Access to Wholesale Prices
   │
   └── Reject  -> Status: `rejected_b2b`
                  └─> Notification Email Sent -> Standard B2C Access
```

---

## 4. SERVER-SIDE BOX QUANTITY VALIDATION

- **HTML Input Control**: `woocommerce_quantity_input_args` sets `min_value` and `step` to `_satem_units_per_box` for approved B2B users.
- **Server Validation (Add to Cart)**: Hook `woocommerce_add_to_cart_validation` enforces `$quantity % $units_per_box === 0`.
- **Server Validation (Cart Update)**: Hook `woocommerce_update_cart_validation` prevents manual tampering of quantities in cart.

---

## 5. SECURITY & PRICE LEAKAGE PROTECTION

- **Role & Capability Escaping**: Non-authenticated guests and unapproved users cannot see or read B2B meta fields.
- **REST API Authorization**: B2B group price meta keys are excluded from public REST responses (`show_in_rest = false` or restricted to `edit_products` capability).
- **Nonce Verification**: All registration and approval actions require valid WordPress Nonces (`wp_verify_nonce`).
- **Capability Check**: Only users with `manage_woocommerce` capability can approve B2B accounts or alter B2B group prices.

---

## 6. AUDIT FINDINGS OF CURRENT `satem-core.php`

| Severity | Issue Description | Recommended Fix |
|---|---|---|
| **Medium** | Missing explicit capability check in `save_packaging_product_fields` | Add `if ( ! current_user_can( 'edit_product', $post_id ) ) return;` |
| **Low** | Capability check in `register_post_meta` uses `edit_posts` | Change to `edit_products` for product capability alignment |

---

## 7. PHASED IMPLEMENTATION PLAN (REQUIRING USER APPROVAL)

- **Phase 11.1**: Refactor `satem-core.php` modular file structure & security capability checks.
- **Phase 11.2**: Implement `Satem_B2B_Roles` & `Satem_B2B_Registration` (Forms, Approval UI & Email notifications).
- **Phase 11.3**: Implement `Satem_B2B_Pricing` (Dynamic memory price filters for B2C/B2B & user overrides).
- **Phase 11.4**: Implement `Satem_B2B_Cart` (Server-side quantity step & cart validation).
