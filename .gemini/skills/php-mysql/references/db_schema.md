# Esquema de Base de Datos - Drawers

Este documento describe la estructura de la base de datos MariaDB utilizada en el proyecto Drawers.

## Tablas Principales

### `drawers_drawer` (Cajones)
- `drawer_id`: ID único (Autoincremental).
- `drawer_name`: Nombre del cajón.
- `drawer_category`: Relación con `drawers_category`.
- `drawer_owner`: Relación con `drawers_usr`.
- `drawer_location`: Ubicación física.
- `drawer_image`: Imagen del cajón (default: `default.png`).
- `drawer_delete`: Flag de borrado lógico (0: activo, 1: borrado).

### `drawers_items` (Objetos)
- `item_id`: ID único.
- `item_drawer`: ID del cajón contenedor.
- `item_name`: Nombre del objeto.
- `item_amount`: Cantidad.
- `item_price`: Precio unitario.
- `item_category`: Categoría del objeto.
- `item_image`: Imagen del objeto.
- `item_delete`: Borrado lógico.

### `drawers_category` (Categorías)
- `category_id`: ID único.
- `category_name`: Nombre (ej: Herramientas, Electrónica).
- `category_color`: Color hexadecimal o clase CSS para visualización.

### `drawers_usr` (Usuarios)
- `usr_id`: ID único.
- `usr_email`: Correo (login).
- `usr_pass`: Contraseña (hasheada).
- `usr_right`: Nivel de permisos (1: Admin, 2: Usuario).

## Convenciones
- **Prefijo:** Todas las tablas usan el prefijo `drawers_`.
- **Charset:** `utf8mb4` con collation `utf8mb4_spanish2_ci`.
- **Borrado Lógico:** Se prefiere el uso de columnas `*_delete` en lugar de `DELETE` físico para mantener integridad referencial y facilitar la recuperación.
