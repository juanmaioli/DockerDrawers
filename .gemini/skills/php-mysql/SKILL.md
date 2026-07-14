---
name: php-mysql
description: Desarrollo y mantenimiento de aplicaciones PHP 8.3 con MariaDB, enfocado en el stack de Drawers (Bootstrap 5.3, mysqli con sentencias preparadas, y API personalizada).
---

# Skill php-mysql

Este skill proporciona guías y patrones para el desarrollo en el ecosistema de **Drawers**.

## 🛠️ Stack Tecnológico
- **Backend:** PHP 8.3
- **Base de Datos:** MariaDB 10.11 (vía `mysqli`)
- **Frontend:** Bootstrap 5.3, jQuery, DataTables
- **Iconografía:** FontAwesome 6

## 📖 Referencias Rápidas
- [Esquema de Base de Datos](references/db_schema.md)
- [Patrones de API](references/api_patterns.md)
- [Convenciones de UI](references/ui_conventions.md)

## 🚀 Flujos de Trabajo Comunes

### 1. Consultas a la Base de Datos
SIEMPRE usar sentencias preparadas para evitar inyecciones SQL.
```php
$sql = "SELECT * FROM drawers_items WHERE item_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
```

### 2. Creación de Nuevas Páginas
Mantener la estructura de `head` y `footer` para asegurar la sesión y el estilo.
```php
<?php include("head.php"); ?>
<main class="container">
    <h1>Nueva Funcionalidad</h1>
    <!-- Contenido -->
</main>
<?php include("footer.php"); ?>
```

### 3. Seguridad y Escapado
Usar la función `h()` definida en `head.php` para todas las salidas de datos dinámicos.
```php
<p>Nombre: <?= h($usr_name) ?></p>
```

### 4. Interacción con la API
Para cargar datos dinámicamente desde JavaScript, usar el endpoint centralizado:
```javascript
fetch(`api/api_drawers.php?id=itemlist-${drawerId}-${userId}`)
    .then(response => response.json())
    .then(data => {
        // Manejar datos
    });
```

## ⚖️ Reglas de Oro
- Nunca usar `mysql_*` (obsoleto). Usar siempre `mysqli` o `PDO`.
- No deshabilitar el reporte de errores de `mysqli` en desarrollo.
- Respetar el prefijo `drawers_` en todas las nuevas tablas o vistas.
- Mantener el soporte para modo oscuro utilizando variables de Bootstrap.
