# Changelog

Todos los cambios importantes de este proyecto se documentarán en este archivo.

El formato está basado en Keep a Changelog
y este proyecto sigue Versionado Semántico (Semantic Versioning).

---

## [0.2.3] - 2026-08-30

### Añadido
- Spinner y mensaje «Cargando vídeo…» mientras se carga el reproductor.
- Estado de error del reproductor con un único botón «Cerrar».
- Mensajes diferenciados para contenido vacío y errores de carga.

---

## [0.2.2] - 2026-08-30

### Mejorado
- Nuevo diseño visual para las tarjetas de vídeo.
- Degradado superior para mejorar la legibilidad del título sobre las miniaturas.
- Botón Play circular, centrado y más discreto.
- Hover sutil y mejoras de presentación responsive.
- Sin cambios en el modal, el reproductor ni la lógica funcional del plugin.

---

## [0.2.1] - 2026-08-30

### Mejorado
- Ayuda integrada y README actualizados para documentar el soporte de playlists.
- Documentación de Playlist ID, URL de playlist, RSS público de YouTube y prioridad de fuentes.
- Documentada la limitación actual de 15 vídeos por playlist del RSS público de YouTube.

---

## [0.2.0] - 2026-08-13

### Añadido
- Soporte para mostrar un vídeo concreto de YouTube mediante el atributo `video` del shortcode.
- Acepta un ID de vídeo y URL de YouTube en los formatos `watch`, `youtu.be` y `shorts`.
- El atributo `video` tiene prioridad sobre `channel` y `limit`.
- Obtención de título y miniatura mediante el endpoint público oEmbed de YouTube, sin API key.
- Caché de metadatos del vídeo manual durante 30 minutos.
- Reutilización de las tarjetas y del modal de reproducción existentes para los vídeos manuales.
- Soporte para playlists públicas de YouTube mediante RSS.
- Acepta Playlist ID y URL de playlist mediante el atributo `playlist`.
- El atributo `playlist` respeta el límite configurado o el atributo `limit` del shortcode.
- Prioridad de fuentes: `video` > `playlist` > `channel`.
- Parser RSS compartido para canales y playlists.
- Implementación de playlists sin API key ni YouTube Data API.

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
