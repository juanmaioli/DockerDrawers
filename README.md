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
    -   **Control de Carga:** Columna con selector de marcado manual (checkbox) y persistencia local en DB para llevar seguimiento de las compras ya registradas en el inventario, con switch para filtrar/ocultar los ya cargados.
    -   **Favoritos de Mercado Libre:** Página (`favoritos_ml.php`) con sistema de **scraping individual y masivo** mediante [api/scrape_fav_item.php](file:///home/juan/VirtualMachines/Docker/Drawers/www_data/api/scrape_fav_item.php). Extrae título, imagen, precio, descripción y detecta de forma automática los distintivos de **Envío FULL** y **Compra Internacional**, almacenando y sincronizando todo en MariaDB (`drawers_fav`).
-   **💵 Gestión de Divisas y Dólar Blue:**
    -   Panel en `admin.php` para el control de la cotización del dólar blue venta, con sincronización automática desde **DolarAPI** y actualización manual.
    -   Calculadora interactiva en pesos/dólares en `item_new.php` con conversión reactiva en tiempo real y persistencia del precio final en dólares.
-   **⭐ Calificación y Ranking de Ítems:**
    -   Sistema de 1 a 5 estrellas interactivas en la ficha de edición del ítem (`item_view.php`).
    -   Nueva columna "Ranking" en el listado de ítems (`items.php`) totalmente ordenable para ver de forma rápida las valoraciones.
-   **🎨 Categorización con Colores:** Identificá rápidamente tus herramientas, componentes o materiales.
-   **🌓 Modo Oscuro/Claro Completo:** Switch directo claro/oscuro con íconos de FontAwesome en la barra de navegación (navbar) sin menús desplegables ni textos, con memoria persistente y adaptación automática para todos los componentes (incluyendo Select2 y DataTables).
-   **🔄 Navegación Dinámica:** Botón de regreso ("Back") inteligente en la vista de ítem, detectando el origen de la llamada para retornar adecuadamente (ya sea al cajón, al listado general o a favoritos).
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
| `v0.16.1` | 🐛 Actualización de etiqueta del switch en `compras.php` a "Sin cargar" (`TODOS / SIN CARGAR`) y perfeccionamiento visual de indicadores `FULL` e `INTER.` |
| `v0.16.0` | 🚀 Switches interactivos `Solo FULL` y `Solo INTER.` en la tabla de Favoritos de Mercado Libre (`favoritos_ml.php`) con filtrado dinámico en DataTables y persistencia en `localStorage` |
| `v0.15.1` | 🐛 Ajustes de diseño y correcciones en Favoritos: imágenes a 90px de alto/ancho circulares, indicadores de `FULL` (verde e itálica sin espacios) e `INTER.` (rojo, itálica, sin espacios y avión rotado -45°) |
| `v0.15.0` | 🚀 Sistema de scraping individual y masivo en Favoritos de Mercado Libre (`favoritos_ml.php` / `api/scrape_fav_item.php`), extracción de precio, título, foto, descripción y detección precisa de Envío FULL y Compra Internacional con sincronización en MariaDB (`drawers_fav`) |
| `v0.14.0` | 🚀 Sincronización automática de favoritos de Mercado Libre a la base de datos `drawers_fav`, creación de columnas `fav_full` e `fav_internacional` (por defecto en `'no'`), renderizado instantáneo y dump completo DDL en `./.dev/admin_drawers.sql` |
| `v0.13.0` | 🚀 Rediseño completo UI/UX del menú desplegable: apertura por hover con tolerancia de 350ms, letras con peso negrita, íconos monocromáticos e integración de perfil de usuario y salida |
| `v0.12.1` | 🐛 Corrección de persistencia de sesión en cookies de "Recordarme en este equipo" habilitando coincidencia dinámica de host y protocolo HTTPS |
| `v0.12.0` | 🚀 Enlaces en nombre y foto de ítems en cajones para ir al detalle, ampliación de imágenes a 90px con bordes dinámicos de categoría, y optimización de diseño a pantalla completa (col-md-12) en la vista de cajón |
| `v0.11.0` | 🚀 Nueva página de Favoritos de Mercado Libre con proxy seguro; "Recordarme" establecido en 30 días; "Agregar al Inventario" abre en nueva pestaña; y marca "Generic" por defecto en vistas de ítem |
| `v0.10.3` | 🐛 Traducción de botón "Print" a "Imprimir" en compras y tabla de pulgadas; estilo de botones DataTable unificado en inches_mm |
| `v0.10.2` | 🌐 Traducción de toda la interfaz (UI), cabeceras de tablas dinámicas y paneles de estadísticas al español (es_AR) |
| `v0.10.1` | 🐛 Corrección de inicialización de estrellas, integración de rating bajo nombre, logo ML más chico y ordenamiento de columna Cargado |
| `v0.10.0` | 🚀 Rediseño de switch de tema en navbar, integración de fondo homogéneo en cards y tablas, y insignias full-width |
| `v0.9.1` | 🚀 Guardado automático de calificación al hacer clic y unificación de tamaño de estrellas |
| `v0.9.0` | 🚀 Implementación de seguimiento de carga en compras, ranking de estrellas y navegación Back dinámica |
| `v0.8.6` | 🧹 Remoción de la clase `text-indigo` de todo el proyecto para consistencia visual |
| `v0.8.5` | 🎨 Select2 adaptado al tema claro/oscuro con variables CSS de Bootstrap |
| `v0.8.4` | 🐛 Corrección de guardado de marcas y mejoras en modal de marcas |
| `v0.8.3` | 🐛 Corrección crítica de decimales en precios (`bind_param`) y carga de modelo |
| `v0.8.2` | 🎨 Formateo visual de 2 decimales en tablas y vistas |
| `v0.8.1` | 🐛 Corrección de CSRF en borrado de ítems |
| `v0.8.0` | 🚀 Integración de cotización del dólar blue y calculadora de divisas |

---
*Mantenido por Juan Gabriel Maioli - Julio 2026*
