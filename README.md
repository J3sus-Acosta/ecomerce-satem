# E-Commerce SATEM (Curaçao)

Repositorio de código personalizado, extensión y documentación para el proyecto de tienda virtual B2C y B2B de **SATEM Soluciones** en Curaçao.

## 📌 Resumen del Proyecto

- **Cliente**: SATEM Soluciones (Curaçao)
- **Plataforma**: WordPress 6.x + WooCommerce 11.0.1
- **Configuración Base**: País Curaçao (`CW`), Moneda `USD` (`$10.00`), Timezone `America/Curacao`.
- **Motor B2B**: **B2BKing Core** v5.2.40 (Evaluación técnica completada; se requiere versión Pro para grupos ilimitados, reglas de caja y precios por rol).
- **Fase Futura**: Sistema de Picking / Escaneo de Códigos de Barras (Barcode Validation).
- **Entorno Actual (Dev)**: WordPress instalado en EasyPanel (`https://tienda.satemsoluciones.com`).
- **Entorno Final (Prod)**: Hostinger (Plan Single) + Dominio definitivo.

---

## 📁 Estructura del Repositorio

```text
ecomerce-satem/
├── docs/             # Documentación técnica y de decisiones del proyecto
│   ├── PROJECT.md    # Especificaciones generales y contexto del negocio
│   ├── ARCHITECTURE.md # Arquitectura WordPress, WooCommerce, plugins y migración
│   └── DECISIONS.md  # Registro de decisiones técnicas tomadas y pendientes
├── plugins/          # Plugins personalizados del proyecto (e.g., satem-core)
├── themes/           # Child theme personalizado de WordPress
├── assets/           # Recursos visuales, logos, fuentes e imágenes del proyecto
├── scripts/          # Scripts de automatización, despliegue y herramientas auxiliares
├── config/           # Configuraciones de entorno y exportaciones (sin credenciales)
├── .gitignore        # Exclusiones de Git para WordPress y entorno local
└── README.md         # Documentación principal del repositorio
```

---

## 🛠️ Desarrollo y Convenciones de Código

1. **Separación de Responsabilidades**: Todo código propio de lógica de negocio va en un plugin dedicado (e.g. `satem-core`). La capa visual personalizada va en un Child Theme.
2. **Estándares WordPress**: PHP 8.0+, WordPress Hooks (`actions` / `filters`), sin modificar core de WordPress ni plugins de terceros.
3. **Control de Versiones**: Este repositorio únicamente guarda código personalizado y documentación. No incluye archivos del core de WordPress ni archivos cargados por usuarios (`wp-content/uploads`).

---

## 📄 Documentación Adicional

- [Proyecto & Requerimientos](file:///d:/Dev/ecomerce-satem/docs/PROJECT.md)
- [Arquitectura Técnica](file:///d:/Dev/ecomerce-satem/docs/ARCHITECTURE.md)
- [Registro de Decisiones](file:///d:/Dev/ecomerce-satem/docs/DECISIONS.md)
