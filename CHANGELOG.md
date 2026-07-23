# Changelog

Todos los cambios importantes de este proyecto se documentarán en este archivo.

El formato está basado en Keep a Changelog
y este proyecto sigue Versionado Semántico (Semantic Versioning).

---

## [0.1.0] - 2026-07-22

### Añadido
- Arquitectura inicial del plugin DMF Media.
- Carga mediante Composer y PSR-4.
- Página de ajustes en el administrador de WordPress.
- Configuración del Channel ID de YouTube.
- Configuración del número máximo de vídeos.
- Shortcode `[dmf_media]`.
- Soporte para atributos `limit` y `channel`.
- Lectura del feed RSS oficial de YouTube.
- Caché para reducir peticiones al feed.
- Botón para vaciar la caché desde el panel de administración.
- Renderizado responsive mediante CSS propio.
- Tarjetas con miniatura y título del vídeo.
- Reproductor mediante modal.
- Reproducción automática del vídeo.
- Cierre del modal con la tecla ESC.
- Cierre del modal al pulsar fuera del vídeo.
- Icono Play en SVG.
- Vista especial cuando solo se muestra un vídeo.
- Ayuda integrada para el uso del shortcode.
- README inicial del proyecto.

### Mejorado
- Validación del límite máximo de vídeos.
- Organización del código siguiendo una arquitectura modular.
- Preparación del plugin para futuras ampliaciones.

### Corregido
- Error que mostraba todos los vídeos al recargar la página.
- Corrección del renderizado cuando solo existe un vídeo.
- Diversas mejoras menores en estabilidad y limpieza del código.

---

## Próximamente

### Planificado (v0.2)

- Soporte para canales mediante `@usuario`.
- Filtros por listas de reproducción.
- Spinner durante la carga del vídeo.
- Animaciones del modal.
- Mejoras de accesibilidad.
- Compatibilidad con YouTube Privacy Enhanced Mode.