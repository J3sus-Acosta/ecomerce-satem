# ARQUITECTURA TÉCNICA Y ESTRATEGIA DE DESARROLLO

## 1. VISIÓN GENERAL DE LA ARQUITECTURA

El proyecto **E-Commerce SATEM** se basa en un ecosistema modular sobre **WordPress Core** y **WooCommerce**, desacoplando la lógica de negocio personalizada del código fuente de terceos.

```text
[ Cliente / Browser ]
        │
        ▼
[ WordPress Core 6.x + WooCommerce ]
  ├── [ Child Theme ] ──> Estilos personalizados, plantillas PHP adaptadas, UI/UX.
  ├── [ Plugin Propio: `satem-core` ] ──> Lógica de negocio (B2B, reglas de caja, hooks).
  ├── [ Plugins de Terceros ] ──> Funcionalidades especializadas (WooCommerce, B2B, Delivery).
  └── [ WP REST API / MCP ] ──> Integración con IA Antigravity y herramientas externas.
```

---

## 2. COMPONENTES PRINCIPALES

### 2.1. Plataforma Base
- **WordPress Core**: Versión estable 6.x+, configurada en idioma Español (`es_VE`), zona horaria `America/Curacao`.
- **WooCommerce**: Instalado y activo (v11.0.1). Configurado para país Curaçao (`CW`) y moneda base `USD` (`$10.00`). Motor principal de e-commerce (catálogo, carrito, checkout, órdenes).

### 2.2. Código Personalizado (Desarrollo Propio)
Todo código propio debe versionarse dentro del repositorio local `ecomerce-satem` y desplegarse mediante archivos sincronizados.
- **Plugin Principal (`plugins/satem-core`)**:
  - Encargado de registrar custom post types, taxonomías, metadata de empaque (unidades por caja), hooks de validación de compra en carrito, roles personalizados B2B y endpoints de la API REST para picking futuro.
- **Tema Hijo (`themes/satem-child`)**:
  - Tema hijo basado en el tema activo oficial (e.g. Twenty Twenty-Four o tema WooCommerce especializado).
  - Contiene sobrerescrituras de plantillas (`woocommerce/`), CSS/JS propios y diseño responsivo.

### 2.3. Plugins de Terceros Evaluados / Recomendados
- **Gestión B2B / Precios Mayoristas**:
  - *Opción Recomendada*: **B2BKing** (o en su defecto combinación modular: *Wholesale Prices for WooCommerce* + *Wholesale Lead Capture* + *Min/Max Quantities*).
  - Permite control total sobre: roles B2B, registro con aprobación previa, precios por rol, cantidades mínimas, múltiplos por caja y visibilidad de categorías por tipo de cliente.
- **Reglas de Envío y Delivery**:
  - *Mecanismo Nativo WooCommerce*: Zonas de Envío + "Envío Gratuito" (con monto mínimo) + "Precio Fijo".
  - *Plugin de Apoyo (si se requiere complejidad)*: **WooCommerce Advanced Free Shipping** o **Table Rate Shipping** para reglas complejas por código postal/distrito de Curaçao.
- **Opciones de Pago**:
  - Modos iniciales: Transferencia Bancaria Directa (BACS) / Pago contra entrega (COD).
  - Gateway futuro: Integración con procesador local de Curaçao (e.g. CX Pay, Banco di Caribe, VPOST / First Atlantic Commerce) o gateway compatible según requerimiento del cliente.

---

## 3. INTEGRACIÓN CON ANTIGRAVITY Y MCP
- **MCP (Model Context Protocol)**:
  - El plugin `wordpress-mcp` (v0.2.5) permanece activo en el entorno de WordPress para permitir la inspección, lectura de estado, verificación de endpoints y asistencia por IA en tiempo real.
- **Control de Código**:
  - La verdad del código fuente radica en el repositorio Git local `ecomerce-satem`. No se edita código a ciegas en producción/staging sin reflejarlo en el repositorio.

---

## 4. ESTRATEGIA DE ENTORNOS Y MIGRACIÓN

### 4.1. Flujo de Trabajo (Dev → Prod)

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

### 4.2. Pasos para la Migración EasyPanel → Hostinger
1. **Sanitización Pre-migración**:
   - Limpieza de datos de prueba no deseados.
   - Verificación de rutas relativas y URLs base.
2. **Exportación de Artefactos**:
   - Base de datos MySQL sanitizada.
   - Directorio `/wp-content/` (plugins, themes, uploads).
3. **Instalación en Hostinger**:
   - Aprovisionamiento de WordPress en Hostinger Plan Single.
   - Configuración de Certificado SSL (Let's Encrypt).
   - Configuración de PHP 8.1+ y límites de memoria (`memory_limit = 512M`, `max_execution_time = 300`).
4. **Post-migración**:
   - Reemplazo de dominio temporal por dominio definitivo del cliente (utilizando WP-CLI `search-replace` o herramientas de migración).
   - Verificación de credenciales, correo SMTP transaccional y llaves de pago.
