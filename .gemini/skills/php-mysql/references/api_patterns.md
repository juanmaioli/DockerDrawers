# Patrones de API - Drawers

La API de Drawers es un endpoint centralizado que maneja consultas y acciones mediante parámetros GET.

## Endpoint Principal
`api/api_drawers.php?id=<tarea>-<parametro1>-<parametro2>`

## Formato de Tareas
Las tareas se definen como el primer segmento del parámetro `id`, separado por guiones (`-`).

### Ejemplos de Tareas Comunes:
1. **`list-<owner_id>-<category_id>`**: Lista los cajones de un usuario, opcionalmente filtrados por categoría.
2. **`view-<drawer_id>`**: Obtiene los detalles de un cajón específico.
3. **`itemlist-<drawer_id>-<owner_id>`**: Lista todos los ítems dentro de un cajón.
4. **`search-<termino>-<owner_id>`**: Realiza una búsqueda global de ítems y cajones.

## Respuesta
La API siempre devuelve un objeto JSON con el encabezado `Content-Type: application/json; charset=utf-8`.

### Ejemplo de Respuesta Exitosa:
```json
{
  "status": "success",
  "data": [...]
}
```

## Seguridad en la API
- **Sentencias Preparadas:** Todas las consultas a la base de datos dentro de la API deben usar `$conn->prepare()` y `$stmt->bind_param()` para evitar inyecciones SQL.
- **Validación de Sesión:** Las acciones que modifican datos deben verificar que el usuario esté autenticado.
