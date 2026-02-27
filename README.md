# 🗄️ Drawers - Gestión de Inventario

**Drawers** es una aplicación web robusta y ligera diseñada para la gestión de inventarios físicos organizados en "cajones". Permite a los usuarios catalogar objetos, asignarles ubicaciones precisas, registrar precios, cantidades y adjuntar imágenes para una identificación visual rápida.

---

## 🚀 Características Principales

- **📦 Organización por Cajones:** Clasifica tus pertenencias en contenedores físicos (drawers).
- **🏷️ Categorización Inteligente:** Agrupa ítems por categorías con colores personalizados.
- **🖼️ Gestión Visual:** Sube fotos de tus cajones e ítems para no perder nada de vista.
- **💰 Control de Costos:** Realiza un seguimiento del precio y valor total de tu inventario.
- **🔐 Sistema de Usuarios:** Gestión de accesos y perfiles personalizada.
- **🔌 API Segura:** Endpoint JSON con protección contra SQL Injection mediante sentencias preparadas.
- **🔖 Marcadores (Bookmarks):** Guarda ítems o búsquedas frecuentes.

---

## 🛠️ Tecnologías Utilizadas

- **Backend:** PHP 8.3 (Apache) con `mysqli` Prepared Statements.
- **Base de Datos:** MariaDB 10.11
- **Frontend:** Bootstrap 5.3, jQuery, DataTables, Select2.
- **Infraestructura:** Docker & Docker Compose.

---

## 📂 Estructura del Proyecto

El repositorio está organizado para separar la lógica de la aplicación del entorno de despliegue:

- **`www_data/`**: Contiene el código fuente completo de la aplicación web (PHP, JS, CSS).
- **`apache_data/`**: Configuraciones personalizadas para el servidor web Apache.
- **`db_data/`**: Directorio para la persistencia de datos de MariaDB (ignorado por Git).
- **`php_data/`**: Archivos de configuración de PHP (`php.ini`).
- **`Dockerfile` & `compose.yml`**: Orquestación y definición de contenedores.

---

## ⚙️ Instalación y Despliegue

### Requisitos Previos
- [Docker](https://www.docker.com/get-started)
- [Docker Compose](https://docs.docker.com/compose/install/)

### Pasos para el Despliegue

1. **Clonar el repositorio:**
   ```bash
   git clone <url-del-repositorio>
   cd Drawers
   ```

2. **Configurar la base de datos:**
   Copia el archivo de ejemplo y ajusta las credenciales si es necesario (por defecto están configuradas para funcionar con el `compose.yml` incluido):
   ```bash
   cp www_data/config_example.php www_data/config.php
   ```

3. **Levantar los servicios:**
   ```bash
   docker compose up -d
   ```

4. **Acceder a la aplicación:**
   Abre tu navegador en `http://localhost:8090`.

---

## 📝 Configuración de la Base de Datos

Si necesitas importar la estructura manualmente, encontrarás el archivo SQL en:
`www_data/drawer_db_structure.sql`

Las credenciales predeterminadas en `compose.yml` son:
- **Database:** `admin_drawers`
- **User:** `juan`
- **Password:** `Lasflores506`

---

## 💾 Mantenimiento de la Base de Datos

### Realizar un Backup (Dump)
Para exportar la base de datos actual a un archivo SQL:
```bash
docker exec mariadb_db mariadb-dump -u juan -pLasflores506 admin_drawers > backup_drawers.sql
```

### Restaurar un Backup
Para importar un archivo SQL a la base de datos:
```bash
docker exec -i mariadb_db mariadb -u juan -pLasflores506 admin_drawers < backup_drawers.sql
```

---

## 🔌 API

La aplicación expone un endpoint en `api/api_drawers.php`. Las consultas se realizan mediante parámetros GET. 
Ejemplo para listar cajones:
`http://localhost:8090/api/api_drawers.php?id=list-1-0`

---

## 📄 Licencia

Este proyecto está bajo la licencia **GNU General Public License v3.0**. Consulta el archivo [LICENSE](www_data/LICENSE) para más detalles.

---
Desarrollado con ❤️ por **Juan Gabriel Maioli**.
