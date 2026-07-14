# 🗄️ Proyecto Drawers - Gestión de Inventario

¡Bienvenido a **Drawers**! Una solución moderna para la gestión de inventarios físicos organizada en "cajones" digitales.

---

## 1. 🚀 Características Principales

-   **📦 Organización por Cajones:** Gestioná tus ítems de forma visual y jerárquica.
-   **🛒 Integración con Mercado Libre:**
    -   Sincronización en tiempo real de tus compras de los últimos 90 días mediante la **API Oficial de Mercado Libre (OAuth 2.0)**.
    -   Visualización de isotipo de Mercado Libre y enlace de acceso rápido a la publicación original en compras para evitar bloqueos del firewall (`PolicyAgent`).
    -   Paginación ilimitada en el backend procesada en el navegador por **DataTables**, compartiendo el mismo diseño visual de botones y exportación que la lista de ítems.
    -   Botón de **creación rápida de ítems** en el inventario prellenado con los datos del producto comprado (Nombre, cantidad, descripción y precio).
-   **💵 Gestión de Divisas y Dólar Blue:**
    -   Panel en `admin.php` para el control de la cotización del dólar blue venta, con sincronización automática desde **DolarAPI** y actualización manual.
    -   Calculadora interactiva en pesos/dólares en `item_new.php` con conversión reactiva en tiempo real y persistencia del precio final en dólares.
-   **🎨 Categorización con Colores:** Identificá rápidamente tus herramientas, componentes o materiales.
-   **🌗 Modo Oscuro/Claro Completo:** Switch de tema que aplica a todos los componentes, incluyendo selects de Select2 (brand, cajón y categoría), cuya paleta de colores se adapta automáticamente a través de variables CSS de Bootstrap 5.3.
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
-   **Frontend:** Bootstrap 5.3, JavaScript (DataTables, jQuery, Select2).
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

## 6. 📋 Historial de Versiones

| Versión | Descripción |
| :--- | :--- |
| `v0.8.5` | 🎨 Select2 adaptado al tema claro/oscuro con variables CSS de Bootstrap |
| `v0.8.4` | 🐛 Corrección de guardado de marcas y mejoras en modal de marcas |
| `v0.8.3` | 🐛 Corrección crítica de decimales en precios (`bind_param`) y carga de modelo |
| `v0.8.2` | 🎨 Formateo visual de 2 decimales en tablas y vistas |
| `v0.8.1` | 🐛 Corrección de CSRF en borrado de ítems |
| `v0.8.0` | 🚀 Integración de cotización del dólar blue y calculadora de divisas |

---
*Mantenido por Juan Gabriel Maioli - Julio 2026*
