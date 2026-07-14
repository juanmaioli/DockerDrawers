# Convenciones de UI - Drawers

El proyecto utiliza Bootstrap 5.3 con una paleta de colores personalizada, centrada en el tono "Indigo".

## Colores Personalizados
Se han extendido las clases de Bootstrap para incluir:
- **`bg-indigo`**: Fondo color índigo.
- **`text-indigo`**: Texto color índigo.
- **`btn-indigo`**: Botones con estilo índigo.
- **`shadow-indigo-sm`**: Sombra suave con tinte índigo.

## Estructura de Páginas
Las páginas siguen una estructura estándar de PHP:
1. `include("head.php")`: Incluye configuración, sesión, conexión a DB y encabezados HTML.
2. Contenido principal envuelto en `<main class="container-fluid">`.
3. `include("footer.php")`: Scripts de cierre y pie de página.

## Componentes Comunes
- **Tarjetas (Cards):** Se usan para mostrar estadísticas y listas de cajones/ítems.
- **DataTables:** Para listas tabulares con búsqueda y ordenamiento avanzado.
- **Select2:** Para desplegables de selección múltiple o con búsqueda interna.
- **Iconos:** Se utiliza FontAwesome 6.

## Funciones de Seguridad en el Frontend
- **Escapado de Salida:** Usar la función global `h($string)` (alias de `htmlspecialchars`) para imprimir cualquier dato proveniente del usuario o de la base de datos.
  ```php
  <?= h($item_name) ?>
  ```
- **CSRF:** El token CSRF está disponible globalmente en JavaScript como `window.csrfToken`.
