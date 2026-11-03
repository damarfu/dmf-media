# Changelog

Todos los cambios importantes de este proyecto se documentarán en este archivo.

El formato está basado en Keep a Changelog
y este proyecto sigue Versionado Semántico (Semantic Versioning).

---

## [0.3.0] - 2026-11-03

### Añadido
- Bloque de Gutenberg `dmf/media`, dinámico, con vista previa en el editor y los mismos datos que el shortcode.
- Inspector con origen (canal de Ajustes, otro canal, playlist o vídeo concreto), límite y offset.
- Soporte de alineación ancha y completa, ancla y clase personalizada.
- Aviso en el editor cuando no hay ningún canal configurado en Ajustes.
- Estilos del bloque dentro del lienzo del editor y vista previa no interactiva.

### Mejorado
- El shortcode y el bloque comparten `MediaResolver`, `Renderer` y el modal; el shortcode sigue disponible y no se deprecia.
- El bloque puede colocarse dentro de contenedores y grids de GenerateBlocks y funciona con cualquier tema.
- Sin dependencias de JavaScript ni proceso de build: el bloque usa las APIs globales de WordPress.

### Calidad
- `uninstall.php` elimina la opción de ajustes y los transients del plugin al desinstalarlo.
- Suite de pruebas sin dependencias (`php tests/run.php` o `composer test`) para parser, IDs, resolver, ajustes, renderer y bloque.
- Feed de ejemplo en `tests/fixtures/feed.xml` y dobles de las funciones de WordPress en `tests/bootstrap.php`.

---

## [0.2.4] - 2026-11-02

### Añadido
- Soporte para omitir los vídeos más recientes de canales y playlists mediante el atributo `offset`.
- Aplicación de `offset` antes de `limit`, sin afectar al modo de vídeo manual.
- Clase `MediaResolver` como punto único de resolución de fuentes, compartida por el shortcode y el futuro bloque.
- Clase `YoutubeId` para normalizar Channel ID, Playlist ID y vídeo a partir de IDs o URLs.
- Modal global impreso una sola vez desde `wp_footer`, fuera del HTML del shortcode, para que ningún contenedor del tema lo recorte o lo atrape en su contexto de apilamiento.
- Estado de error del reproductor tras 8 segundos sin carga (embed bloqueado o red caída).
- Delegación de eventos: las tarjetas insertadas por AJAX también abren el modal.
- Bloqueo del fondo con `inert` y compensación del ancho de scroll al abrir el modal.

### Mejorado
- CSS responsive del modal según la altura de la ventana y rejilla sin desbordes en móviles estrechos.
- Foco visible en tarjetas y controles, y respeto a `prefers-reduced-motion`.
- Los assets solo se cargan en páginas que usan el shortcode o el bloque, con fallback al renderizar.
- El JS funciona con carga diferida y contenido dinámico (comprueba `readyState` y se inicializa al primer clic).
- Miniaturas con `alt` vacío para no duplicar el título y `decoding="async"`.

### Corregido
- Se comprueba el código HTTP y el `Content-Type` antes de guardar un feed en caché, evitando cachear páginas de error durante 30 minutos.
- Caché negativa de 5 minutos para no repetir peticiones a YouTube cuando un feed o un oEmbed fallan.
- Validación del Channel ID con mensajes claros; se admite el ID o una URL `/channel/UC...`.
- Validación del Playlist ID y del vídeo antes de hacer peticiones, con mensajes específicos.
- Soporte de URLs de playlist de `m.youtube.com`, `music.youtube.com`, `watch?...&list=`, `embed` y playlists `UU`, `LL`, `FL`, `RD` y `OLAK5uy_`.
- Soporte de URLs de vídeo `m.youtube.com`, `embed` y `live`.
- Validación del ID de vídeo procedente del RSS antes de usarlo.
- Los ajustes se validan y sanitizan al guardarlos; un Channel ID incorrecto conserva el valor anterior y muestra un aviso.
- El límite máximo pasa de 20 a 15 vídeos, el máximo real que entrega el RSS de YouTube.
- `limit="0"` o un valor fuera de rango se ajustan a 1–15 en lugar de ignorarse.
- Un valor corrupto en las opciones ya no provoca un error fatal en el frontend.
- El modelo `Video` admite fecha de publicación nula (el vídeo manual no la conoce).

### Eliminado
- Archivos vacíos `src/Core/Autoloader.php` y `src/Cache/Cache.php`.

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
