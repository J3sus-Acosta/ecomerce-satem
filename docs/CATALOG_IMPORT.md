# GUIDE FOR CATALOG IMPORT — E-COMMERCE SATEM (CURAÇAO)

This document provides the technical guidelines and specifications for populating the product catalog template (`catalog-template.csv`). Please follow these instructions carefully to ensure smooth automated import into WooCommerce.

---

## 1. MANDATORY COLUMN SPECIFICATIONS

| Column Header | Data Type | Required? | Example Value | Technical Description & Rules |
|---|---|---|---|---|
| `SKU` | **TEXT** | **Mandatory** | `BEB-001` | Unique Stock Keeping Unit. Must be text format. Never use currency or formula formatting. Must be 100% unique per product. |
| `Name` | **TEXT** | **Mandatory** | `Orange Juice 1L` | Commercial product title in English. |
| `Short Description` | **TEXT** | **Mandatory** | `1L pure orange juice container` | Brief summary shown on single product card. |
| `Description` | **TEXT** | **Mandatory** | `Fresh 1L pure orange juice in paper carton.` | Detailed technical and nutritional description. |
| `Regular Price` | **DECIMAL** | **Mandatory** | `2.50` | B2C retail public price in USD (`$`). **Do NOT include `$` symbol**. Use `.` as decimal separator. |
| `Stock` | **INTEGER** | **Mandatory** | `1200` | Total inventory in **individual units** (e.g. 100 boxes of 12 = `1200`). Must be a whole number. |
| `Categories` | **TEXT** | **Mandatory** | `Beverages` | Product category name. Use consistent category titles. |
| `_satem_barcode_unit` | **TEXT** | **Mandatory** | `0759123456789` | EAN-13 / UPC-A printed on the single item. **Must be formatted as TEXT** to preserve leading zeros. |
| `_satem_barcode_box` | **TEXT** | Optional | `17591234567896` | EAN-14 / ITF-14 printed on the master carton. Must be formatted as TEXT. |
| `_satem_units_per_box`| **INTEGER** | **Mandatory** | `12` | Number of individual items contained in 1 master box (e.g. `12`). Must be a whole number. |
| `_satem_sku_box` | **TEXT** | Optional | `BOX-BEB-001` | Manufacturer SKU code for the master carton. |

---

## 2. RECOMMENDED OPTIONAL COLUMNS & JUSTIFICATION

| Column Header | Data Type | Reason for Inclusion |
|---|---|---|
| `Brand` | TEXT | Allows filtering products by manufacturer/brand on the storefront. |
| `Weight` | DECIMAL | Required in kg for automated shipping & freight calculation. |
| `Length` | DECIMAL | Package length in cm for box volume calculation. |
| `Width` | DECIMAL | Package width in cm for box volume calculation. |
| `Height` | DECIMAL | Package height in cm for box volume calculation. |
| `Image URL` | TEXT | Direct HTTP/HTTPS link to product image for automatic bulk upload. |
| `Tax Class` | TEXT | Defines tax rule (`standard` or `zero-rate`) once Curaçao OB % is confirmed. |
| `Status` | TEXT | Controls visibility (`publish` for active items, `draft` for hidden items). |

---

## 3. CRITICAL RULES FOR CLIENT DATA PREPARATION

> [!IMPORTANT]
> **12 MANDATORY RULES TO PREVENT IMPORT ERRORS**:
> 1. **Do NOT use Excel formulas**: Convert all cell values to plain text/values before saving.
> 2. **Do NOT merge cells**: Every row must be an independent product.
> 3. **Format Barcodes as TEXT**: Barcodes starting with `0` (e.g., `0759123456789`) will lose the leading zero if formatted as numbers. Set column format to Text in Excel/CSV.
> 4. **No Currency Symbols in Price**: Enter `10.50` instead of `$10.50` or `10.50 USD`.
> 5. **Decimal Separator**: Use dot (`.`) for decimals (e.g., `2.50`), never comma (`,`).
> 6. **Unique SKUs**: No two products can share the same SKU.
> 7. **Unique Barcodes**: Unit barcodes must be unique per product item.
> 8. **Stock in Individual Units**: Always enter inventory as individual units, not boxes (e.g. 10 boxes of 12 = `120`).
> 9. **Units per Box must be an Integer**: Whole number greater than 0 (e.g. `6`, `12`, `24`).
> 10. **Consistent Category Names**: Ensure exact spelling for categories (e.g. do not mix `Beverage` and `Beverages`).
> 11. **No Accidental Spaces**: Remove leading/trailing whitespaces in SKU or barcodes (` BEB-001 `).
> 12. **English Language**: All names, descriptions, and categories should be in English.

---

## 4. FUTURE IMPORT WORKFLOW & ARCHITECTURE

```text
CLIENT (Provides CSV/Excel)
       │
       ▼
1. DATA AUDIT (Verification of headers, data types, and formatting)
       │
       ▼
2. NORMALIZATION (Trim spaces, ensure TEXT format for barcodes/SKU)
       │
       ▼
3. DRY-RUN VALIDATION (Detect duplicate SKUs/Barcodes, invalid prices)
       │
       ▼
4. SATEM APPROVAL (Sign-off on normalized dataset)
       │
       ▼
5. WOOCOMMERCE BULK IMPORT (Import products & SATEM packaging meta)
       │
       ▼
6. MCP AUDIT & REST API VERIFICATION (Validate product IDs & meta fields)
       │
       ▼
7. STOREFRONT TESTING (Verify B2C single unit purchasing & B2B box rules)
       │
       ▼
8. GO LIVE (Catalog operational)
```

---

## 5. SECURITY & VALIDATION RULES FOR FUTURE IMPORTER

Any automated import script developed in future phases MUST enforce the following security and validation controls:
- **Header Validation**: Reject file if mandatory columns (`SKU`, `Name`, `Regular Price`, `Stock`, `_satem_barcode_unit`, `_satem_units_per_box`) are missing.
- **Duplicate Detection**: Flag and block import if duplicate SKUs or duplicate unit barcodes are present.
- **Data Type Sanitization**:
  - `Regular Price`: Must be numeric > 0.
  - `Stock`: Must be non-negative integer.
  - `_satem_units_per_box`: Must be positive integer >= 1.
  - `_satem_barcode_unit` & `_satem_barcode_box`: Sanitized using `sanitize_text_field` to prevent XSS while preserving strings with leading zeros.
- **Transaction Safety & Rollback**: Perform import inside database transactions or dry-run validation to avoid partial catalog corruption upon error.
- **Detailed Error Log**: Emit row-level error reports specifying exact line numbers and failed fields.

---

## 6. SAMPLE DATA ROW (EXAMPLE — DO NOT IMPORT)

```csv
SKU,Name,Short Description,Description,Regular Price,Stock,Categories,_satem_barcode_unit,_satem_barcode_box,_satem_units_per_box,_satem_sku_box,Brand,Weight,Length,Width,Height,Image URL,Tax Class,Status
"EXAMPLE-001","Orange Juice 1L (EXAMPLE - DO NOT IMPORT)","1L pure orange juice container","Fresh 1L pure orange juice in paper carton.",2.50,1200,"Beverages","0759123456789","17591234567896",12,"BOX-BEB-001","SampleBrand",1.10,10,10,24,"https://example.com/sample-image.jpg","standard","publish"
```
