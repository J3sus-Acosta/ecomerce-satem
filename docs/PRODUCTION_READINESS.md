# PRODUCTION READINESS & DEPLOYMENT SPECIFICATION — E-COMMERCE SATEM

This document defines the operational architecture, staging-to-production workflow, backup strategy, and rollback procedures for launching **E-Commerce SATEM** in Curaçao.

---

## 1. INFRASTRUCTURE & ENVIRONMENT REQUIREMENTS

- **Server Stack**: Apache/2.4 + PHP 8.1+ (Recommended PHP 8.3) + MariaDB 10.11+ / MySQL 8.0+.
- **SSL / HTTPS**: Mandatory SSL certificate enforced across all domain endpoints (`https://tienda.satemsoluciones.com`).
- **PHP Memory & Limits**:
  - `memory_limit` >= 256M (128M minimum).
  - `upload_max_filesize` >= 64M.
  - `post_max_size` >= 64M.
  - `max_execution_time` >= 120s.

---

## 2. DEPLOYMENT & VERSION CONTROL WORKFLOW

```text
LOCAL DEVELOPMENT (d:\Dev\ecomerce-satem)
       │
       ▼
LOCAL GIT REPOSITORY (Commit & Verification)
       │
       ▼
STAGING ENVIRONMENT (Testing B2C/B2B Flows)
       │
       ▼
PRODUCTION ENVIRONMENT (Go-Live Deployment)
```

### Git Repository Scoping Guidelines
- **Tracked Paths**: `plugins/satem-core/`, `themes/satem-child/`, `docs/`, `catalog-template.csv`, `README.md`.
- **Excluded Paths (`.gitignore`)**: WordPress core files (`wp-admin/`, `wp-includes/`), WooCommerce upload media (`wp-content/uploads/`), server config files (`wp-config.php`, `.htaccess`).

---

## 3. BACKUP & ROLLBACK STRATEGY

### Database & File Backup Protocol
1. **Automated Daily Backups**: Database SQL dump and `wp-content/` directory backups retained for 30 rolling days.
2. **Pre-Deployment Snapshots**: Full database snapshot taken prior to any production plugin or catalog import action.

### Rollback Procedure
- If a critical failure occurs during deployment or catalog import:
  1. Restore database snapshot to previous clean state.
  2. Restore original `plugins/satem-core/` and `themes/satem-child/` version tag.
  3. Flush Object Cache & Transients via WP-CLI (`wp cache flush`).

---

## 4. HPOS & SECURITY READINESS

- **High-Performance Order Storage (HPOS)**: HPOS order table datastore is fully active (`wp_wc_orders`). All order actions execute using WooCommerce CRUD APIs without postmeta fallback dependencies.
- **Price Leakage Security**: B2B meta fields (`_satem_price_b2b_*`) remain strictly restricted from public REST endpoints (`show_in_rest = false`).
