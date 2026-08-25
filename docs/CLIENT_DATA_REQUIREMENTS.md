# CLIENT COMMERCIAL DATA REQUIREMENTS — E-COMMERCE SATEM (CURAÇAO)

This document provides a comprehensive inventory of all pending commercial, legal, fiscal, logistical, and catalog data required from the client prior to final production configuration.

---

## 1. COMPANY & LEGAL DATA

- [ ] **Legal Company Name**: Full registered legal entity name in Curaçao.
- [ ] **Physical Address**: Official business address, street name, building number, district, and postal code.
- [ ] **Commercial Contact Details**: Primary customer support email, phone number, and official WhatsApp number.
- [ ] **Legal Pages Content**:
  - Terms & Conditions text.
  - Privacy Policy text.
  - Shipping, Returns & Refund Policy text.

---

## 2. FISCAL & TAX CONFIGURATION (OB - OMZETBELASTING)

- [ ] **Sales Tax Rate (OB %)**: Official tax percentage applicable to retail (B2C) products in Curaçao (e.g., 6%, 7%, 9% or exempt).
- [ ] **Display Rule**: Specify whether storefront prices must include tax or if tax should be calculated at checkout.
- [ ] **Wholesale B2B Tax Exemption**: Confirm whether approved B2B buyers (`b2b_toko`, `b2b_restaurant`, `b2b_supermarket`) are tax-exempt or follow standard tax rules.

---

## 3. COMMERCIAL CATALOG & PRODUCT MATRIX

- [ ] **Commercial Import File**: Final catalog populated using `catalog-template.csv`.
- [ ] **Product SKUs & Barcodes**: Individual EAN-13 / UPC-A unit barcodes and EAN-14 / ITF-14 master box barcodes.
- [ ] **Wholesale Packaging Matrix**: Units per master box (`_satem_units_per_box`) for each product.
- [ ] **B2B Group Pricing Matrix**:
  - B2C Retail Price ($)
  - Toko Wholesale Price ($)
  - Restaurant Wholesale Price ($)
  - Supermarket Wholesale Price ($)
- [ ] **Product Photography**: High-resolution catalog images hosted or provided for bulk upload.

---

## 4. PAYMENT GATEWAY CONFIGURATION

- [ ] **Local Curaçao Gateway**: Confirm primary payment processor (e.g., CX Pay, First Atlantic Commerce, VPOST, Stripe International, or PayPal).
- [ ] **Offline Payment Methods**:
  - Confirm if Cash on Delivery (COD) is offered.
  - Confirm if Local Bank Transfer (BACS) is offered.
  - Provide official Bank Name, Account Name, Account Number, and SWIFT/IBAN instructions for Curaçao transfers.

---

## 5. LOGISTICS & SHIPPING RULES

- [ ] **Free Shipping Threshold**: Minimum order amount ($) required for free delivery (B2C and B2B).
- [ ] **Flat Rate Shipping Fee**: Fixed delivery charge ($) if free shipping threshold is not met.
- [ ] **Geographic Shipping Zones**: Specific districts or zones in Curaçao with differentiated delivery fees.
- [ ] **Local Warehouse Pickup**: Confirm if warehouse pickup (Local Pickup) is enabled.
