# ARQUITECTURA TÉCNICA Y ESTRATEGIA DE DESARROLLO

## 1. VISIÓN GENERAL DE LA ARQUITECTURA

El proyecto **E-Commerce SATEM** se basa en un ecosistema modular sobre **WordPress Core** y **WooCommerce**, desacoplando la lógica de negocio personalizada del código fuente de terceros.

```text
[ Cliente / Browser ]
        │
        ▼
[ WordPress Core 6.x + WooCommerce ]
  ├── [ Child Theme (`satem-child`) ] ──> Estilos nativos (CSS/JS), templates PHP adaptados, UI/UX.
  ├── [ Plugin B2B (`B2BKing`) ] ──> Gestión de grupos B2B, precios, cajas, MOQ y registro comercial.
  ├── [ Plugin Propio (`satem-core`) ] ──> Meta fields de empaque/barcode y endpoints de picking.
  └── [ WP REST API / MCP ] ──> Integración con IA Antigravity y herramientas externas.
```

---

## 2. COMPONENTES PRINCIPALES

### 2.1. Plataforma Base
- **WordPress Core**: Versión estable 6.x+, configurada en idioma principal **Inglés (`en_US`)**, zona horaria `America/Curacao`.
- **WooCommerce**: Instalado y activo (v11.0.1). Configurado para país Curaçao (`CW`) y moneda base `USD` (`$10.00`). Motor principal de e-commerce (catálogo, carrito, checkout, órdenes).
- **B2BKing Core**: Instalado y activo (v5.2.40). Evaluación técnica completada.

### 2.2. Código Personalizado (Desarrollo Propio)
Todo código propio debe versionarse dentro del repositorio local `ecomerce-satem` y desplegarse mediante archivos sincronizados.
- **Plugin Principal (`plugins/satem-core`)**:
  - Esqueleto limpio configurado con compatibilidad HPOS. Registra meta fields nativos de empaque (`_satem_barcode_unit`, `_satem_barcode_box`, `_satem_units_per_box`) y proporciona endpoints REST API para el sistema de picking.
- **Tema Hijo (`themes/satem-child`)**:
  - Tema hijo basado en Twenty Twenty-Four. Contiene la maquetación nativa, CSS variables del sistema de diseño y scripts responsivos sin acoplamiento a Page Builders.

---

## 3. REGLAS ARQUITECTÓNICAS DEL FRONTEND

### 3.1. Prohibición Total de Page Builders (ADR-014)
Queda estrictamente prohibida la instalación, activación o incorporación de constructores visuales de páginas (Page Builders), tales como:
- Elementor / Elementor Pro
- Divi / Beaver Builder / Bricks / WPBakery / Visual Composer / Oxygen / Breakdance
- Frameworks de bloques pesados (Kadence, Spectra, Ultimate Addons)

**Razón**: Garantizar el máximo rendimiento web, portabilidad, independencia técnica, código limpio versionable en Git y compatibilidad fluida con WooCommerce Blocks, HPOS, B2BKing y el sistema de picking.

### 3.2. Idioma Principal de Interfaz (ADR-015)
- El idioma oficial comercial del storefront es **English (`en_US`)**.
- Todas las etiquetas de interfaz (Shop, Cart, Checkout, My Account, navegación, botones, formularios, alertas y avisos del sistema) se presentan en inglés.

---

## 4. B2B ARCHITECTURE (WOOCOMMERCE + B2BKING)

El modelo de e-commerce híbrido para SATEM Soluciones operará bajo las siguientes reglas arquitectónicas:

### 4.1. Clientes B2C (Particulares)
- **Acceso**: Público general (sin registro previo obligatorio o con checkout invitado).
- **Unidades**: Venta unitaria de productos.
- **Precios**: Precios regulares de lista (PVP).

### 4.2. Clientes B2B (Mayoristas: Tokos, Restaurantes, Supermercados)
- **Estructura de Grupos B2B**:
  - `Toko`: Descuento/precio base mayorista para minimarkets `[REQUERIMIENTO DE LICENCIA PRO]`.
  - `Restaurante`: Precios para el canal HORECA `[REQUERIMIENTO DE LICENCIA PRO]`.
  - `Supermercado`: Precios para cadenas de volumen `[REQUERIMIENTO DE LICENCIA PRO]`.
- **Precios por Grupo & Cliente**:
  - *Precios por Grupo*: Matriz de precios por producto diferenciada por grupo `[REQUERIMIENTO DE LICENCIA PRO]`.
  - *Precios por Cliente Individual*: Override de precios personalizados por usuario `[REQUERIMIENTO DE LICENCIA PRO]`.
- **Venta por Caja, MOQ y Múltiplos (Quantity Steps)**:
  - Regla de compra por empaque completo (Min: 12, Step: 12, Max: 120) `[REQUERIMIENTO DE LICENCIA PRO]`.
- **Flujo de Registro y Aprobación**:
  - Formulario de registro B2B con campos comerciales obligatorios (CRIB, KVK) y aprobación manual `[REQUERIMIENTO DE LICENCIA PRO]`.

> [!IMPORTANT]
> **REGLA DE DESARROLLO**: `satem-core` **NO** duplicará código ni funcionalidades que B2BKing Pro proporciona de forma nativa. Si una característica requiere la versión Pro, permanece documentada como dependencia comercial para su contratación directa por el cliente final.

---

## 5. DISEÑO DEL MODELO DE CATÁLOGO (UNIDAD VS CAJA)

Se adopta la **Opción A (Producto Único Simple con Metadatos de Empaque)** como arquitectura oficial para el catálogo.

### 5.1. Mapeo de Campos de Producto

| Dato del Producto | Almacenamiento Técnico | Responsable | Uso |
|---|---|---|---|
| **SKU Unitario** | `_sku` (Nativo WC) | WooCommerce | Identificador base de producto |
| **Nombre / Descripción** | `post_title` / `post_content` | WooCommerce | Ficha técnica y SEO |
| **Categoría** | Taxonomía `product_cat` | WooCommerce | Navegación y estructura de menú |
| **Precio B2C** | `_regular_price` | WooCommerce | Precio de lista retail público |
| **Stock Total** | `_stock` | WooCommerce | Inventario unificado en unidades |
| **Peso y Dimensiones** | `_weight`, `_length`, `_width`, `_height` | WooCommerce | Cálculo logístico y flete |
| **Código de Barras Unitario** | Meta `_satem_barcode_unit` | `satem-core` | Escaneo en picking (unidad) |
| **Código de Barras de Caja** | Meta `_satem_barcode_box` | `satem-core` | Escaneo en picking (caja máster) |
| **Unidades por Caja** | Meta `_satem_units_per_box` | `satem-core` | Factor de empaque (ej. 12) |
| **SKU de Caja (Opcional)** | Meta `_satem_sku_box` | `satem-core` | Referencia de empaque de fábrica |
| **Precios Mayoristas B2B** | Precios por Grupo / User Meta | `B2BKing` `[PRO]` | Matriz de precio B2B por rol/cliente |
| **Paso de Cantidad (Step/MOQ)** | Reglas Dinámicas / Step | `B2BKing` `[PRO]` | Restricción de compra en múltiplos de caja |

---

## 6. INTEGRACIÓN CON ANTIGRAVITY Y MCP
- **MCP (Model Context Protocol)**:
  - El plugin `wordpress-mcp` (v0.2.5) permanece activo en el entorno de WordPress para permitir la inspección, lectura de estado, verificación de endpoints y asistencia por IA en tiempo real.
- **Control de Código**:
  - La verdad del código fuente radica en el repositorio Git local `ecomerce-satem`.

---

## 7. ESTRATEGIA DE ENTORNOS Y MIGRACIÓN

### 7.1. Flujo de Trabajo (Dev → Prod)

```text
[ Entorno Local / Antigravity ]
        │ (Desarrollo de Plugins/Themes y Docs)
        ▼
[ Repositorio Git (`ecomerce-satem`) ]
        │ (Sincronización de código)
        ▼
[ WordPress en EasyPanel (`tienda.satemsoluciones.com`) ] (Entorno Staging/Dev)
        │ (Auditoría, QA y aprobación del cliente)
        ▼
[ Migration Export (All-in-One WP Migration / Duplicator / CLI) ]
        │
        ▼
[ WordPress en Hostinger Single ] (Entorno Producción Final)
```
