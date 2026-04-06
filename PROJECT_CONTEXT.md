# SwiftField Project Context & Governance

## 1. Core Stack & Architecture
- **Name:** SwiftField (Micro-SaaS Field Services Engine).
- **Framework:** Laravel 12.x (PHP 8.3+).
- **Admin Panel:** Filament v5 (Multi-panel: Admin & SuperAdmin).
- **Frontend:** TALL Stack (Tailwind, Alpine.js, Livewire 3 + Volt).
- **Database:** PostgreSQL (JSONB Optimized).
- **Pattern:** Layered Architecture (Simplified DDD).
    - **Controllers:** Orchestration only (Request -> DTO -> Service -> Resource).
    - **DTOs:** Immutable objects for data transfer between layers.
    - **Services:** Pure business logic, domain rules, and third-party orchestration.
    - **Repositories:** Eloquent/Query abstraction only.

## 2. Engineering Excellence (World-Class Standards)
Cualquier implementación debe regirse por estos principios para garantizar escalabilidad:

- **Interface-First (Strategy Pattern):** Antes de integrar servicios externos (WhatsApp, Pagos, SMS), se debe definir un `Contract` (Interface). El sistema debe operar con la abstracción, no con el driver concreto.
- **Dependency Inversion (DIP):** Las clases de alto nivel no deben depender de las de bajo nivel. Ambas deben depender de abstracciones.
- **Event-Driven Design:** Las acciones secundarias (notificaciones, logs, puntos de fidelidad) deben ejecutarse mediante `Domain Events` y `Listeners` desacoplados.
- **Result Object Pattern:** Los Services deben retornar un objeto `Result` (success, data, errors) en lugar de lanzar excepciones para flujo de negocio o devolver booleanos simples.
- **Hot-Swapping Config:** Los límites, montos y flags de comportamiento deben residir en base de datos (`tenant_settings`) para permitir cambios sin despliegue (Zero-Deploy).
- **Idempotency:** Operaciones críticas (pagos, agendas) deben usar `idempotency_keys` para evitar duplicados.

## 3. Agent Skills & Protocols
- **[LARAVEL_ARCHITECT]:** Integridad de capas, SOLID, Patrones de Diseño.
- **[SECURITY_ENGINEER]:** Multi-guard Isolation (Session isolation), UUIDs, RBAC.
- **[DEVOPS_GIT_MANAGER]:** Storage links, permisos de servidor, CI/CD, local vs prod sync.
- **[FRONT_END_ARCHITECT]:** Atomic Design, UX Mobile-first, TALL Stack components.

## 4. Business Logic & Roles
- **Multi-tenancy:** Identificación por Path (`/{tenant_slug}`). Aislamiento total de datos.
- **Session Isolation:** Guards separadas (`web` para tenants, `superadmin` para plataforma).
- **Dynamic Services:** Definiciones de campos vía JSONB en la entidad `services`.

## 5. Database Dictionary (Critical)
- **tenants:** Identity, branding, WhatsApp config, slug.
- **services:** Catalog, JSONB field_definitions.
- **bookings:** Transactions, status_history, JSONB custom_values.
- **users:** Admins con `is_super_admin` flag y `tenant_id`.

## 6. Response Protocol (Iterative Workflow)
1. **Análisis de Dominio:** Impacto en el tenant y usuario final.
2. **Propuesta de Diseño:** Definir Interface, DTO, Service y Repo antes de codear.
3. **Confirmación:** Esperar "Procede".
4. **Implementación:** Código limpio, tipado estricto, comentarios de patrón usado.