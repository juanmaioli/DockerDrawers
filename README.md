# 🗄️ Proyecto Drawers - Gestión de Inventario

¡Bienvenido a **Drawers**! Una solución moderna para la gestión de inventarios físicos organizada en "cajones" digitales.

---

## 1. 🚀 Características Principales

-   **📦 Organización por Cajones:** Gestioná tus ítems de forma visual y jerárquica.
-   **🛒 Integración con Mercado Libre:**
    -   Sincronización en tiempo real de tus compras de los últimos 90 días mediante la **API Oficial de Mercado Libre (OAuth 2.0)**.
    -   Visualización de isotipo de Mercado Libre y enlace de acceso rápido a la publicación original en compras para evitar bloqueos del firewall (`PolicyAgent`).
    -   Paginación ilimitada en el backend procesada en el navegador por **DataTables**.
    -   Botón de **creación rápida de ítems** en el inventario prellenado con los datos del producto comprado (Nombre, cantidad, descripción y precio).
-   **🎨 Categorización con Colores:** Identificá rápidamente tus herramientas, componentes o materiales.
-   **📈 Optimización de Rendimiento:** API refactorizada con `JOINs` complejos e índices `FULLTEXT` para búsquedas instantáneas.
-   **⚡ Alta Velocidad:** Habilitación de OPcache en PHP y optimizaciones en la base de datos MariaDB.
-   **🛡️ Seguridad Reforzada:** 
    -   Validación estricta de tipos MIME en subida de archivos (prevención de RCE).
    -   Capa global de escape Anti-XSS en frontend (JS) y backend (PHP).
    -   Protección completa contra ataques CSRF en todos los formularios (incluyendo Login) y acciones AJAX.
    -   Sistema de borrado seguro mediante peticiones POST validadas.
    -   Consultas protegidas con sentencias preparadas (PDO/MySQLi).
-   **🔐 Seguridad SSL:** Soporte para dominios locales `*.drawers.docker` en el puerto `8443`.
-   **📸 Gestión de Imágenes:** Soporte para fotos de cajones e ítems.

---

## 2. ⚙️ Requisitos y Despliegue

### Requisitos:
-   [Docker](https://www.docker.com/) & [Docker Compose](https://docs.docker.com/compose/)
-   Un navegador moderno (Chrome, Brave, Firefox)

### Inicio Rápido:
```bash
# Iniciar todos los servicios
docker compose up -d

# Detener los servicios
docker compose down
```

### Acceso al Sistema:
-   **HTTP:** [http://localhost:8090](http://localhost:8090)
-   **HTTPS:** [https://drawers.docker:8443](https://drawers.docker:8443)

> **Nota:** Recordá configurar tu archivo de hosts (`/etc/hosts`) para el dominio `drawers.docker`.

---

## 3. 🛠️ Arquitectura Técnica

-   **Backend:** PHP 8.3 (Apache) con OPcache habilitado.
-   **Base de Datos:** MariaDB 10.11 (LTS).
-   **Frontend:** Bootstrap 5.3, JavaScript (DataTables, jQuery).
-   **Infraestructura:** Dockerizado para un despliegue sin fricciones.

---

## 4. 📂 Estructura del Proyecto

-   `www_data/`: Código fuente de la aplicación (PHP/JS/CSS).
-   `apache_data/`: Configuraciones de Apache y certificados SSL.
-   `php_data/`: Archivo `php.ini` optimizado.
-   `db_data/`: Persistencia de la base de datos MariaDB.

---

## 5. 🏗️ Guía de Desarrollo

Para contribuir o modificar el sistema:
1.  **SSL:** Consultá `SSL_CONFIG.md` para regenerar certificados.
2.  **Base de Datos:** El esquema se encuentra en `www_data/drawer_db_structure.sql`.
3.  **API:** Las consultas se manejan en `www_data/api/api_drawers.php`.

---
*Mantenido por Juan Gabriel Maioli - Marzo 2026*
