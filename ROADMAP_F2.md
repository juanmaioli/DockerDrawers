# 🗺️ Roadmap Drawers - Fase 2

Este documento detalla las tareas de optimización, seguridad y mejora de arquitectura para el proyecto **Drawers**.

---

## 1. 🔐 Consolidación de Seguridad y Autenticación
- [x] Refactorizar `head.php` para usar Sentencias Preparadas.
- [x] Implementar verificación de permisos dinámica basada en `usr_right` (eliminar IDs hardcodeados).
- [x] Unificar la lógica de validación de sesión/cookie en una función centralizada.

## 2. 🧹 Limpieza de Deuda Técnica
- [ ] Corregir typos en la base de datos (`descriptinon` -> `description`).
- [ ] Refactorizar el código PHP/JS para reflejar los nombres de columnas correctos.
- [ ] Centralizar la creación de la conexión `$conn` en `config.php`.

## 3. 📈 Optimización de API y Frontend
- [ ] Implementar manejo de errores robusto (Try-Catch) en la API.
- [ ] Persistir la preferencia de vista (Cards/Table) en `localStorage`.
- [ ] Mejorar la carga de assets (minificación opcional).

## 4. 🐳 Mejora de Infraestructura
- [ ] Migrar credenciales de `config.php` a variables de entorno en Docker.
- [ ] Documentar el proceso de backup de la base de datos.

---
*Plan generado por Gemini el 26/02/2026.*
