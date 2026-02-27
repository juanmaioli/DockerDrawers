# 🗺️ Roadmap Drawers - Fase 3: Modernización y UX

Esta fase se centra en mejorar la interactividad, la búsqueda global y la modernización de la interfaz de usuario.

---

## 1. 🔍 Búsqueda Global Inteligente
- [x] Implementar un buscador global en la barra de navegación que filtre ítems y cajones simultáneamente.
- [x] Refactorizar el caso `search` en `api_drawers.php` para soportar consultas por texto (LIKE).
- [x] Crear una vista de resultados de búsqueda dinámica.

## 2. 💅 Modernización de Interfaz (UI/UX)
- [ ] **Modo Oscuro Nativo:** Integrar el toggle actual con las variables de CSS de Bootstrap 5.3 (`data-bs-theme`).
- [ ] **Feedback Visual:** Agregar *spinners* de carga durante las peticiones AJAX a la API.
- [ ] **Modales de Acción:** Reemplazar el flujo de "Nueva página" por modales para agregar/editar categorías y marcas, evitando recargas innecesarias.

## 3. 📊 Dashboard y Estadísticas
- [ ] Mejorar las tarjetas de estadísticas en `index.php` con gráficos simples (ej. Chart.js o barras CSS).
- [ ] Agregar métricas de "Últimos ítems agregados" y "Cajones más llenos".

## 4. 🧹 Calidad de Código y Seguridad (Plus)
- [ ] **Protección CSRF:** Implementar tokens de seguridad en todos los formularios POST.
- [ ] **Refactorización de JS:** Organizar `app.js` en módulos o un objeto literal para evitar contaminar el scope global.

---
*Propuesta generada por Gemini el 26/02/2026.*
