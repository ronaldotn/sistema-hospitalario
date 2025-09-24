# 🏥 Historia Clínica Electrónica Interoperable (HCEi)

La **Historia Clínica Electrónica (HCEi)** es un sistema modular diseñado para gestionar, almacenar y compartir de forma segura la información clínica de los pacientes entre diferentes instituciones de salud. Cumple estándares internacionales como **HL7 FHIR**, **OAuth2/OIDC** y las mejores prácticas de **seguridad, privacidad y auditoría**.

Este repositorio documenta su arquitectura, funcionalidades y módulos principales.

---

## 🎯 Objetivos

* Centralizar y unificar la información clínica de pacientes en un sistema interoperable.
* Garantizar la seguridad, trazabilidad y consentimiento en el acceso a datos de salud.
* Facilitar la integración entre hospitales, laboratorios, farmacias y otros actores del ecosistema.
* Cumplir regulaciones locales e internacionales sobre protección de datos (ej. GDPR, HIPAA).
* Brindar una base flexible y extensible para proyectos de salud digital.

---

## 🧩 Módulos Principales

### 1. **Gestión de Pacientes**

* Registro único de pacientes con algoritmos de detección de duplicados.
* CRUD de pacientes con soporte a identificadores múltiples (CI, pasaporte, seguro).
* Interoperabilidad vía recursos FHIR `Patient`.

### 2. **Registro Clínico**

* Creación y gestión de **encuentros médicos** (`Encounter`).
* Registro de diagnósticos (`Condition`), observaciones (`Observation`), tratamientos y órdenes médicas.
* Adjuntos y resultados de laboratorio (`DiagnosticReport`, `DocumentReference`).

### 3. **Gestión de Profesionales de la Salud**

* Registro y validación de médicos, enfermeros y especialistas (`Practitioner`).
* Asociación de profesionales a organizaciones (`Organization`) y roles.
* Verificación de licencias y certificaciones.

### 4. **Seguridad y Gestión de Identidad**

* Autenticación robusta con **OAuth2 / OIDC** (SSO, MFA, WebAuthn).
* **Autorización** mediante RBAC + ABAC y scopes FHIR.
* Gestión del ciclo de vida de cuentas (onboarding SCIM, desactivación, reactivación).
* **Consentimiento del paciente** y mecanismo *break-glass* para emergencias.
* Auditoría completa con eventos de acceso (`AuditEvent`).

### 5. **Interoperabilidad y APIs**

* API FHIR RESTful para compartir datos clínicos.
* Soporte de exportación/ingesta de documentos CDA y JSON FHIR.
* API Gateway con **mTLS, rate limiting y WAF**.

### 6. **Auditoría y Cumplimiento**

* Registro de cada acción crítica en `AuditEvent`.
* Dashboard de auditoría para administradores.
* Exportación hacia SIEM (Splunk, ELK).

### 7. **Infraestructura y DevSecOps**

* CI/CD con validaciones SAST/DAST, pruebas automatizadas y escaneo de dependencias.
* Gestión de secretos con **Vault/KMS**.
* Observabilidad (Prometheus + Grafana + ELK).

---

## 🛠️ Arquitectura

* **Frontend**: Vue + Tailwind + shadcn/ui.
* **Backend**:PHP/Laravel (microservicios).
* **APIs**: HL7 FHIR R4 RESTful.
* **Seguridad**: Keycloak para IdP, OPA para políticas ABAC, Redis para sesiones.
* **Base de datos**: PostgreSQL (estructurado).

---

## Configuración recomendada del IDE

[VS Code](https://code.visualstudio.com/) + [Volar](https://marketplace.visualstudio.com/items?itemName=johnsoncodehk.volar) (and disable Vetur).

## Customize configuration

Consulte [Referencia de configuración de Vite](https://vitejs.dev/config/).

## Configuración del proyecto

### Requerimientos

```sh
 "php": ">=5.6.4"
```

### Instalación

Ejecutar los siguientes comandos en orden

```cmd
git clone https://github.com/ronaldotn/sistema-hospitalario.git
```

```cmd
cd sistema-hospitalario
```

```cmd
composer install
```

Seguidamente recuerda que por seguridad el archivo <b>"<em>.env</em>"</b> no se copia, para ello dispones del mismo pero con el nombre
<b>"<em>.env.example</em>"</b> el cual deberás renombrar a <b>"<em>.env</em>"</b> solamente.

Recuerda también ingresar en el archivo <b>"<em>.env</b>"</em> los datos de conexión a la base de datos que deberas haber creado previamente, esto es importante para poder continuar con el siguiente paso y generar el <b>"<em>key</b>"</em>.

```cmd
php artisan key:generate
```

```cmd
php artisan migrate:install
```

```cmd
php artisan migrate
```

```cmd
php artisan db:seed

php artisan migrate:fresh --seed

php artisan serve
```

### Para instalar dependencias

```sh
npm install
```

### Compilar y Hot-Reload para el desarrollo

```sh
npm run dev
```

### Type-Check, compilar y minimizar para producción

```sh
npm run build
```

---

## 🤝 Contribuciones

Este proyecto es de código abierto y busca la colaboración de la comunidad para mejorar la digitalización del sector salud.

* Reporta issues y bugs.
* Propón nuevas funcionalidades vía Pull Requests.
* Participa en las discusiones para mejorar estándares de interoperabilidad.

---

## 📜 Licencia

Distribuido bajo licencia **MIT**.

---
