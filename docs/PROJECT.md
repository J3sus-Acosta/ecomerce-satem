# PROYECTO: E-COMMERCE SATEM (CURAÇAO)

## 1. OBJETIVO DEL PROYECTO
Desarrollar una tienda online robusta, escalable y mantenible sobre **WordPress + WooCommerce** para el cliente **SATEM Soluciones**, ubicado en **Curaçao**. La plataforma atenderá tanto el canal minorista (B2C) como el canal mayorista/comercial (B2B), con capacidad futura de integración de un módulo de picking y validación de pedidos mediante códigos de barras.

---

## 2. CONTEXTO DEL CLIENTE Y UBICACIÓN
- **Ubicación del Negocio**: Curaçao.
- **Mercado Objetivo**: Consumidores finales (residentes/turistas) y clientes comerciales locales (Tokos, minimarkets, supermercados, restaurantes y hoteles).
- **Moneda Base**: Dólar Estadounidense (USD - `$`).
- **Zona Horaria**: `America/Curacao` (UTC-4).
- **Idioma Principal**: Español (con capacidad de soporte multilingüe Papiamento / Holandés / Inglés en fases posteriores si se requiere).

---

## 3. MODELOS DE NEGOCIO

### 3.1. B2C — Clientes Particulares (Retail)
- **Modalidad**: Venta al por menor por unidad.
- **Precios**: Precios regulares de lista (PVP).
- **Flujo de Compra**: Carrito estándar de WooCommerce → Checkout rápido → Pago online/contra entrega → Delivery o Retiro.
- **Delivery**:
  - Requiere un **monto mínimo de pedido** para habilitar la entrega a domicilio.
  - Tarifa fija o por zona de entrega dentro de Curaçao.
  - Opción de **Envío Gratuito** cuando el pedido supere un valor determinado (e.g. `$X`).
- **Cuentas**: Soporte para registro de cliente opcional o compra como invitado (Guest Checkout) según definición final del cliente.

### 3.2. B2B — Clientes Comerciales (Wholesale / B2B)
- **Tipos de Clientes B2B**: Tokos, supermercados, minimarkets, restaurantes, empresas de catering, etc.
- **Registro y Aprobación**:
  - Formulario de registro B2B dedicado con solicitud de datos fiscales/comerciales.
  - Flujo de **aprobación manual por parte del administrador** antes de otorgar acceso a precios mayoristas.
- **Estructura de Precios Mayoristas**:
  - Precios diferidos por rol/grupo de cliente (e.g. *B2B Standard*, *Toko Gold*, *Restaurante VIP*).
  - Posibilidad de **precios específicos por cliente individual**.
  - Descuentos escalonados por volumen de compra.
- **Reglas de Empaque y Cantidades**:
  - Compra por caja / bulto (Case / Master Carton).
  - Definición de **cantidades mínimas por producto** (MOQ - Minimum Order Quantity).
  - Regla de **múltiplos de compra** (e.g. incrementos de 6, 12 o 24 unidades por caja).

---

## 4. LOGÍSTICA Y DELIVERY (CURAÇAO)
- **Zonas de Entrega**: Configuración de zonas específicas dentro de Curaçao (ej. Willemstad, Banda Abou, Banda Riba).
- **Reglas de Despacho**:
  - Monto mínimo por pedido para habilitar delivery.
  - Costo de envío variable o plano según zona.
  - Envío gratis condicionado a monto total.
  - Opción de "Local Pickup" (Retiro en depósito/tienda).
- **Impuestos (Tax Rate)**: Queda pendiente de definición con el contador/cliente. No se deben aplicar reglas tributarias arbitrarias hasta recibir confirmación oficial.

---

## 5. FASE FUTURA: SISTEMA DE PICKING / BARCODE
En una segunda fase, la plataforma integrará una herramienta de picking para operadores de almacén:
- **Funcionalidad**: Interfaz web/mobile optimizada para escaneo con lector de código de barras (1D/2D EAN/UPC).
- **Verificación**:
  - Lectura de SKU / Barcode del empaque.
  - Validación del producto correcto contra las líneas del pedido.
  - Validación de cantidad empacada vs. cantidad solicitada.
  - Alertas visuales y auditivas en caso de escaneo erróneo.
  - Indicador de progreso de armando del pedido ("Order Prepared / Ready for Dispatch").

---

## 6. INFRAESTRUCTURA Y ENTORNOS

### 6.1. Entorno de Desarrollo (Actual)
- **Plataforma**: WordPress en **EasyPanel** (Docker containerized WP).
- **WooCommerce**: Instalado y activo (v11.0.1).
- **Configuración Regional**: País Curaçao (`CW`), Moneda Dólar Estadounidense (`USD` - `$10.00`), Zona Horaria `America/Curacao`.
- **URL**: `https://tienda.satemsoluciones.com`
- **Conectividad**: MCP (`wordpress-mcp` v0.2.5) activo para auditoría e integración.

---

## 7. ESTADO DE DEFINICIONES Y PENDIENTES (FASE 1)
- [x] **WooCommerce**: Instalado y configurado en estado base.
- [x] **Configuración Regional**: Curaçao, USD ($), America/Curacao.
- [ ] **Impuestos**: Pendiente definición tributaria de Curaçao (OB %).
- [ ] **Pagos**: Pasarela de pago pendiente de selección por parte del cliente.
- [ ] **Delivery**: Tarifas y zonas pendientes de confirmación del cliente.
- [ ] **B2B**: Pausado para evaluación manual de plugins por parte del usuario.
- [ ] **Branding**: Diseño visual, paleta de colores y logo pendientes de definición.
- [ ] **Picking / Barcode**: Pospuesto para Fase 2.


### 6.2. Entorno de Producción (Previsto)
- **Hosting**: **Hostinger** (Plan Single).
- **Plataforma**: WordPress + WooCommerce.
- **Estrategia**: Desarrollo 100% aislado en EasyPanel → Exportación limpia de base de datos y assets → Despliegue en Hostinger mediante proceso de migración controlado.
