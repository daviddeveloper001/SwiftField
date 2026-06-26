# 🛡️ MODO GUARDIA: SINGLE SOURCE OF TRUTH (SSOT)

> [!IMPORTANT]
> **ESTE ARCHIVO ES LA ÚNICA FUENTE DE VERDAD.**
> Cualquier agente de IA (Gemini, Claude, Cursor) DEBE leer este archivo íntegramente antes de proponer cambios, generar código o ejecutar comandos. Ignorar estas directrices resultará en deuda técnica y desalineación arquitectónica.

---

## 🚀 1. VISIÓN GLOBAL: StockFlow

**StockFlow** es un SaaS de gestión de inventarios multi-tenant diseñado específicamente para **micro-ecommerce y tiendas locales**. 

- **Propuesta de Valor:** Utilidad inmediata, curva de aprendizaje cero, bajo costo operativo y una UI premium extremadamente rápida.
- **Diferenciadores Clave:** 
    - **Kardex basado en eventos (Immutable Ledger):** Cada movimiento es un evento histórico; el stock actual es una proyección.
    - **Escáner móvil nativo:** Optimizado para cámaras de smartphones mediante integración web/PWA.
    - **Multi-tenancy Aislado:** Seguridad total entre clientes desde el core.

---

## 🛠️ 2. CONSIDERACIONES TÉCNICAS MANDATORIAS

### Stack Tecnológico
- **Backend:** PHP 8.4+ & Laravel 13 (Bleeding Edge).
- **Frontend Admin:** Filament v5 & Livewire 3/4.
- **Estilos:** Tailwind CSS v4 (Configuración CSS-first, sin `tailwind.config.js`).
- **Base de Datos:** PostgreSQL (Optimizado para queries complejas y JSONB).
- **Testing:** Pest v4 (Sintaxis funcional y Architecture Testing).

### Arquitectura de Software
- **Patrón:** Action-Based Clean Architecture.
- **Reglas de Oro:**
    1. **Actions/UseCases Únicos:** La lógica de negocio vive en clases `Action` dedicadas. Los controladores y comandos son meros disparadores.
    2. **Tipado Estricto:** Prohibido el uso de `mixed`, `any` o tipos ambiguos. Se deben definir `array shapes` en PHPDoc.
    3. **Inmutabilidad del Kardex:** El stock nunca se "edita", se registran movimientos (In/Out/Adjust).
    4. **Zero-Downtime Migrations:** Diseñar esquemas que permitan evolución sin bloqueos.

---

## 🧠 3. ÍNDICE DE SKILLS & DISPARADORES (@triggers)

He detectado físicamente los siguientes skills en `.agents/skills/`. Úsalos activamente:

| Skill | Trigger | Propósito | Ruta Relativa |
| :--- | :--- | :--- | :--- |
| **Action-Based Architecture** | `@architecture` | Decisiones de sistema y Clean Architecture. | `.agents/skills/action-based-architecture/SKILL.md` |
| **Atomic UI** | `@atomic-ui` | Diseño Atómico en Filament v5. | `.agents/skills/atomic-ui/SKILL.md` |
| **Brainstorming** | `@brainstorming` | Planificación de datos y lógica antes de codear. | `.agents/skills/brainstorming/SKILL.md` |
| **Database Design** | `@database-design` | Arquitectura de base de datos y esquemas. | `.agents/skills/database-design/SKILL.MD` |
| **Doc Coauthoring** | `@doc-coauthoring` | Documentación evolutiva de módulos. | `.agents/skills/doc-coauthoring/SKILL.md` |
| **Filament Multi-Tenancy** | `@filament-multitenancy` | Desarrollo UI y lógica multi-tenant. | `.agents/skills/filament-multitenancy/SKILL.md` |
| **Laravel Best Practices** | `@laravel-best-practices` | Reglas de oro del framework. | `.agents/skills/laravel-best-practices/SKILL.md` |
| **Laravel Expert** | `@laravel-expert` | Optimización y estándares modernos. | `.agents/skills/laravel-expert/SKILL.md` |
| **Pest Testing** | `@pest-testing` | Tests unitarios, feature y de arquitectura. | `.agents/skills/pest-testing/SKILL.md` |
| **Tailwind CSS v4** | `@tailwindcss-development` | Utilidades CSS y diseño responsivo. | `.agents/skills/tailwindcss-development/SKILL.md` |

**Triggers de Lógica de Negocio (Virtuales):**
- `@kardex-logic`: Para todo lo relacionado con integridad transaccional e inmutabilidad.
- `@business-growth`: Para decisiones de UX/UI orientadas a conversión y retención.

---

## 🗺️ 4. ROADMAP DE IMPLEMENTACIÓN INICIAL

### Fase 1: Core & Tenancy 🏗️ (COMPLETADA)
- **Foco:** Configuración de base de datos multi-tenant y acceso administrativo.
- **Hito:** Dashboard vacío pero accesible bajo un identificador de tenant.

### Fase 2: Catálogo Scoped 📦 (COMPLETADA)
- **Foco:** Gestión de Productos, Categorías y Atributos aislados por Tenant.
- **Skills Clave:** `@laravel-expert`, `@atomic-ui`, `@architecture`.
- **Hito:** CRUD de productos funcionando con **Actions** dedicados y **Atributos Dinámicos** por categoría.

### Fase 3: Kardex Inmutable 🔄 (COMPLETADA)
- **Foco:** Motor de movimientos de stock y auditoría histórica.
- **Skills Clave:** `@kardex-logic`, `@database-design`, `@pest-testing`.
- **Hito:** Motor de `RecordStockMovementAction` operativo, proyección de stock y **Auditoría Ledger** en UI.

### Fase 4: Movilidad & PWA 📱 (COMPLETADA)
- **Foco:** Experiencia nativa y escaneo de códigos.
- **Skills Clave:** `@tailwindcss-development`, `@atomic-ui`.
- **Hito:** StockFlow instalable como PWA con **Escáner de SKU** integrado mediante cámara.

### Fase 5: Dashboard Inteligente 📊 (COMPLETADA)
- **Foco:** Visualización de métricas críticas y salud del negocio.
- **Skills Clave:** `@laravel-expert`, `@business-growth`.
- **Hito:** Dashboard con widgets de **Stock Bajo**, **Valoración de Inventario** y **Tendencias**.

### Fase 6: Proveedores & Trazabilidad 🤝 (COMPLETADA)
- **Foco:** Gestión de la cadena de suministro y costos de entrada.
- **Hito:** CRUD de **Proveedores** y vinculación de movimientos de entrada con su origen y costo.

### Fase 7: Valoración & Reportes Financieros 💰 (COMPLETADA)
- **Foco:** Cálculo del valor real del inventario y exportación de datos.
- **Skills Clave:** `@laravel-expert`, `@database-design`.
- **Hito:** Widget de **Valoración Total** en el Dashboard y exportación de reportes de stock a **Excel/PDF**.

### Fase 8: Roles & Permisos 🔐 (COMPLETADA)
- **Foco:** Seguridad granular y control de acceso.
- **Hito:** Spatie configurado, roles (Admin/Manager/Staff) y políticas de protección financiera activas.

### Fase 9: Gestión de Equipo e Invitaciones 👥 (COMPLETADA)
- **Foco:** Crecimiento del tenant y colaboración.
- **Skills Clave:** `@architecture`, `@filament-multitenancy`.
- **Hito:** Sistema de **Invitaciones por Correo** y gestión de miembros del equipo con roles asignados.

### Fase 10: Clientes y Trazabilidad de Salidas 👥📦 (COMPLETADA)
- **Foco:** Gestión de cartera de clientes y vinculación de ventas.
- **Hito:** CRUD de **Clientes**, selección dinámica en el modal de movimientos (para Salidas) y captura de precio de venta.

### Fase 11: Analítica Financiera y Márgenes 📈 (COMPLETADA)
- **Foco:** Visibilidad de ingresos, gastos y margen bruto.
- **Hito:** Dashboard financiero con cálculo en tiempo real de ventas del mes y rentabilidad basado en metadatos JSONB.

### Fase 12: Facturación SaaS y SuperAdmin 👑 (EN PROCESO)
- **Foco:** Monetización, control de acceso por suscripción y panel maestro.
- **Skills Clave:** `@architecture`, `@filament-multitenancy`.
- **Hito:** Bloqueo por middleware de tenants expirados, subida de comprobantes de pago y Panel SuperAdmin para aprobar/extender suscripciones.

---

## 📝 5. PROTOCOLO DE DOCUMENTACIÓN

Cada vez que un módulo o funcionalidad principal sea finalizada, el agente debe:
1. Crear/Actualizar la documentación técnica en `docs/modules/[nombre-modulo].md`.
2. Utilizar el skill `@doc-coauthoring` para asegurar que la documentación sea legible y útil para futuros desarrollos.
3. Actualizar el registro de cambios (si existe) o el estado en este SSOT si el roadmap evoluciona.

---
*Este documento es dinámico. Si la arquitectura cambia significativamente, actualiza este SSOT primero.*
