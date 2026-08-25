# REGISTRO DE DECISIONES TÉCNICAS (ADR - Architecture Decision Records)

Este documento contiene las decisiones técnicas tomadas para el proyecto **E-Commerce SATEM**, así como el catálogo de decisiones pendientes que requieren confirmación del cliente o equipo de negocio.

---

## 1. DECISIONES TÉCNICAS ADOPTADAS

| ID | Fecha | Tema | Decisión Tomada | Razón / Justificación |
|---|---|---|---|---|
| ADR-001 | 2026-08-24 | Stack Tecnológico | WordPress 6.x + WooCommerce | Requerimiento explícito del cliente para facilitar la administración y hosting objetivo (Hostinger Single). |
| ADR-002 | 2026-08-24 | Estructura de Código | Repositorio enfocado únicamente en código propio (`plugins/`, `themes/`, `docs/`) | Evitar ensuciar el repositorio con el core de WordPress o archivos de uploads de medios. |
| ADR-003 | 2026-08-24 | Extensión B2B | Lógica mediante Plugin de Negocio Propio + Plugin B2B especializado de terceros | Mantener el código modular, fácil de versionar e independiente de temas visuales. |
| ADR-004 | 2026-08-24 | Configuración Geográfica Base | Curaçao (`America/Curacao`, USD `$`) | Ajustado a la ubicación de operaciones del cliente. |
| ADR-005 | 2026-08-24 | Fase Picking / Barcode | Pospuesto para Fase 2 (Prevista arquitectura compatible con REST API) | Centrar la Fase 1 en la estabilización de la tienda B2C/B2B antes de añadir la capa logística avanzada. |
| ADR-006 | 2026-08-24 | Lógica B2B de Clientes | Pausado desarrollo propio; el cliente revisará manualmente plugins existentes | Priorizar el uso de un plugin ya existente para evitar desarrollo desde cero innecesario. |
| ADR-007 | 2026-08-24 | Configuración Fiscal / Impuestos | `PENDIENTE — DEFINICIÓN TRIBUTARIA DE CURAÇAO` | Sin tasa de impuesto configurada en WooCommerce hasta recibir confirmación oficial del cliente/contador. |
| ADR-008 | 2026-08-24 | Pasarela de Pago | `PENDIENTE — PASARELA DE PAGO` | Sin gateways activos (Stripe, PayPal, CX Pay, etc.) hasta conocer el proveedor de pagos real del cliente. |
| ADR-009 | 2026-08-24 | Zonas y Tarifas de Delivery | `PENDIENTE — TARIFAS Y ZONAS DE DELIVERY` | Estructura preparada sin crear zonas ni tarifas ficticias hasta contar con las reglas de envío finales. |
| ADR-010 | 2026-08-24 | Selección de Plugin B2B | Evaluación técnica de **B2BKing Core** | Instalado B2BKing Core v5.2.40. Evaluación completada: 100% compatible. **Sin compras de licencias de ningún tipo**. La implementación B2B se basará exclusivamente en módulos gratuitos o soluciones sin costo. |
| ADR-011 | 2026-08-24 | Política de Licenciamiento Comercial | Proyecto para **único cliente / único sitio**. Las licencias comerciales serán contratadas/costeadas directamente por el cliente final. | SATEM no asume licencias como costo propio. Se desarrolla con versiones Free/Lite y se etiquetan funcionalidades como `[REQUERIMIENTO DE LICENCIA PRO]` cuando se requiera la versión Pro. |
| ADR-012 | 2026-08-24 | Modelo de Catálogo (Unidad vs Caja) | **Opción A: Producto Único Simple con Metadatos de Empaque** | Inventario unificado. B2C compra por unidad; B2B compra por caja usando reglas de paso (Step 12) de B2BKing. Campos de empaque (`_satem_barcode_unit`, `_satem_barcode_box`, `_satem_units_per_box`) preparados para el picking futuro. |
| ADR-013 | 2026-08-24 | Auditoría Base WooCommerce | **Configuración Base para Curaçao Verificada** | CW / USD / America/Curacao confirmados. Impuestos, pasarelas de pago, zonas de delivery y grupos B2B quedan formalmente registrados como `[PENDIENTE — DEFINICIÓN DEL CLIENTE]` para evitar configuraciones arbitrarias. |
| ADR-014 | 2026-08-24 | Frontend sin Page Builder | **Prohibición Total de Page Builders** | Prohibidos Elementor, Divi, Bricks, WPBakery, Oxygen, Breakdance y equivalentes. Desarrollo mediante `satem-child`, WooCommerce nativo, bloques y código propio para garantizar rendimiento, mantenibilidad y control. |
| ADR-015 | 2026-08-24 | Idioma Principal de Interfaz | **ENGLISH (en_US)** | El idioma comercial oficial de la plataforma es inglés. Toda la interfaz (Shop, Cart, Checkout, My Account, avisos) se presenta en inglés. |
| ADR-016 | 2026-08-24 | Modelo de Datos y Códigos de Barras | **Tratamiento de Barras como Cadena (String) e Inventario Unificado** | Los códigos de barras (`_satem_barcode_unit`, `_satem_barcode_box`) se almacenan strictly como texto (`string`) para preservar ceros a la izquierda (ej. `0759...`). El stock se mantiene en unidades únicas compartidas por B2C y B2B. |
| ADR-017 | 2026-08-24 | Evaluación de Arquitectura B2B Propia | **Viabilidad Técnica de Motor B2B Propio Nativo en `satem-core`** | Evaluada la arquitectura para desarrollar un motor B2B ligero en `satem-core` utilizando User Roles, User Meta y WC Hooks. Elimina la necesidad de licencias de pago de terceros, garantiza un 100% de rendimiento HPOS y control total del código. |

---

## 2. DECISIONES PENDIENTES (REQUERIMIENTOS DEL CLIENTE)

Las siguientes definiciones deben ser provistas por el cliente o contador antes de proceder a la configuración definitiva del sistema:

### 2.1. Branding y Aspecto Visual
- [ ] Logo vectorizado y manual de marca (paleta de colores, tipografías corporativas).
- [ ] Banners, slider inicial o imágenes de categorías de producto.
- [ ] Decisión sobre tema visual base (Tema en bloque FSE vs Tema WooCommerce dedicado como Astra/Flatsome/Storefront).

### 2.2. Gateway de Pagos
- [ ] ¿Qué proveedor de pagos bancarios / pasarela se utilizará en Curaçao? (CX Pay, First Atlantic Commerce, VPOST, Stripe Internacional, Paypal, etc.).
- [ ] ¿Se ofrecerá pago contra entrega (Cash on Delivery - COD) o transferencia bancaria local (BACS)?
- [ ] Datos de la cuenta bancaria para transferencias en Curaçao (si aplica).

### 2.3. Configuración Fiscal e Impuestos (Taxes)
- [ ] ¿Qué tasa de impuesto sobre las ventas (OB - Omzetbelasting) aplica a los productos en Curaçao? (e.g. 6%, 7%, 9% o exento).
- [ ] ¿Los precios mostrados en la tienda deben incluir el impuesto o calcularse al checkout?
- [ ] ¿Los clientes B2B están exentos de impuestos o aplican la misma regla fiscal?

### 2.4. Reglas Logísticas y Delivery
- [ ] Monto mínimo exacto de compra requerido para habilitar el servicio de delivery B2C.
- [ ] Monto de compra a partir del cual el delivery es GRATUITO.
- [ ] Costo fijo de envío si no se alcanza el monto gratis.
- [ ] Zonas o distritos específicos de Curaçao con tarifas diferenciadas.
- [ ] ¿Se permitirá el retiro en almacén/tienda física (Local Pickup)?

### 2.5. Catálogo de Productos y Precios
- [ ] Listado de productos en formato Excel/CSV con: SKU, Nombre, Descripción, Categoría, Precio B2C, Unidades por Caja.
- [ ] Definición de imágenes de producto (fotografía de catálogo).
- [ ] Matriz de precios B2B (Precio por caja, precios diferidos para Tokos, Supermercados, Restaurantes).
- [ ] Múltiplos de compra por caja (MOQ y tamaño de bulto).

### 2.6. Flujo de Clientes B2B
- [ ] ¿Los clientes comerciales requieren aprobación previa obligatoria por correo/admin antes de ver precios B2B?
- [ ] Formulario de registro B2B: ¿Qué campos fiscales/legales específicos se solicitan? (KVK number, CRIB number, etc.).
- [ ] Términos de pago B2B (Pago inmediato online, contra entrega, o crédito a 15/30 días si aplica).

### 2.7. Contenido Legal e Informativo
- [ ] Términos y Condiciones de Uso.
- [ ] Política de Privacidad y Tratamiento de Datos.
- [ ] Política de Envíos, Devoluciones y Reembolsos.
- [ ] Información de contacto, dirección física y WhatsApp de soporte.
