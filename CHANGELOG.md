# Changelog

Todos los cambios importantes de este proyecto se documentarán en este archivo.

El formato está basado en Keep a Changelog
y este proyecto sigue Versionado Semántico (Semantic Versioning).

---

## [0.2.0] - 2026-08-13

### Añadido
- Soporte para mostrar un vídeo concreto de YouTube mediante el atributo `video` del shortcode.
- Acepta un ID de vídeo y URL de YouTube en los formatos `watch`, `youtu.be` y `shorts`.
- El atributo `video` tiene prioridad sobre `channel` y `limit`.
- Obtención de título y miniatura mediante el endpoint público oEmbed de YouTube, sin API key.
- Caché de metadatos del vídeo manual durante 30 minutos.
- Reutilización de las tarjetas y del modal de reproducción existentes para los vídeos manuales.

### Mejorado
- Página de ajustes más clara para la configuración inicial de DMF Media.
- Ayuda integrada ampliada con la documentación de los shortcodes disponibles.
- Documentación del vídeo manual y de la prioridad del atributo `video`.
- Explicación del Channel ID y del límite predeterminado de vídeos.
- Enlace directo «Ajustes» desde la pantalla de Plugins de WordPress.

---

## [0.1.1] - 2026-07-24

### Corregido
- Se evita renderizar más de un modal cuando hay varios shortcodes en la misma página.
- Las tarjetas mantienen un enlace directo al vídeo como alternativa si JavaScript no está disponible.
- Correcciones de coherencia entre los metadatos y la documentación del proyecto.

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
