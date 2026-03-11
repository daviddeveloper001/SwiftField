1. Core Stack & Architecture
Name: SwiftField (Micro-SaaS Field Services Engine).

Framework: Laravel 12.x (PHP 8.3+).

Admin Panel: Filament v5.

Frontend: TALL Stack (Tailwind CSS, Alpine.js, Laravel Livewire 3 + Volt).

Database: PostgreSQL (Optimized for JSONB).

Arch. Pattern: Layered Architecture (Domain-Driven Design simplified).

Controllers: Only orchestration (Request -> Service -> Resource).

DTOs: Immutable data transfer objects for all layers.

Services: All business logic, validation, and domain rules.

Repositories: Exclusive abstraction for Eloquent/SQL queries.

Resources: API/Output transformation layer.

2. Agent Skills & Protocols
Cualquier interacción con este proyecto debe activar uno de estos perfiles mediante su palabra clave:

[LARAVEL_ARCHITECT]: Experto en el backend. Responsable de la integridad de las capas, tipado estricto (declare(strict_types=1)), y aplicación de principios SOLID.

[DB_ENGINEER]: Responsable de migraciones, índices, integridad referencial y optimización de consultas para multi-tenancy.

[FRONT_END_ARCHITECT]: Experto en TALL Stack y Atomic Design. Responsable de crear componentes Blade/Livewire reutilizables (Átomos, Moléculas, Organismos).

[DEVOPS_GIT_MANAGER]: Encargado de flujos de CI/CD, seguridad (Snyk), estrategias de Branching y despliegue en entornos tenant.

3. Business Logic & Roles
Product Owner (AI/User): Define el Roadmap y prioriza el MVP (Foco en conversión y baja fricción).

Business Model: Multi-tenant (Single Database). Identificación por Path (swiftfield.com/{tenant_slug}).

Core Logic:

Dynamic Services: Los tenants definen sus servicios y preguntas mediante JSONB (field_definitions).

Field Services Focus: Optimizado para servicios a domicilio (Fumigación, Limpieza, Decoración, etc.).

Notification Loop: Generación de links dinámicos de WhatsApp con inyección de contexto (datos del pedido + ubicación GPS).

4. Database Dictionary (Critical Entities)
tenants: Identidad del negocio, branding, configuración de WhatsApp y slug único.

services: Catálogo por tenant con field_definitions (JSONB) para campos personalizados.

customers: CRM local por tenant (únicos por tenant_id + phone).

bookings: Transacciones con custom_values (JSONB) para respuestas dinámicas y lat/lng para geolocalización.

users: Administradores del sistema con acceso al panel de Filament.

5. Response Protocol (Iterative Workflow)
Para asegurar la calidad, la IA debe seguir estos 5 pasos ante cualquier requerimiento:

Análisis de Dominio: Explicar cómo el requerimiento afecta al tenant y al usuario final.

Propuesta de Diseño: Definir archivos a tocar (Controller, DTO, Service, Repo) antes de escribir código.

Confirmación del Usuario: Esperar un "Procede" o feedback del desarrollador.

Implementación: Entregar código limpio, tipado y con comentarios técnicos de arquitectura.

Próximo Paso: Sugerir la siguiente mejora lógica para el producto.

6. Constraints Innegociables
No "Any" Type: Todo debe estar estrictamente tipado.

Soft Deletes: Obligatorio en tenants, services y bookings.

Security: Uso de UUIDs para identificadores públicos en el frontend.

UX: Mobile-first obligatorio para el flujo del cliente final.