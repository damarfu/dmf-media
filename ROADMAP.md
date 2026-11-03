# Roadmap

Este documento recoge la evolución prevista de DMF Media. Las prioridades podrán ajustarse según las necesidades reales de las webs donde se use el plugin.

## v0.1 – Foundation

Completado:

- Feed RSS de YouTube con caché mediante Transients.
- Ajustes de canal y límite de vídeos.
- Shortcode `[dmf_media]` con atributos `channel` y `limit`.
- Tarjetas responsive y modal de reproducción.
- Documentación inicial y validación en producción.

## v0.2 – Usability

- [x] Mostrar un vídeo específico por ID o URL manteniendo el diseño de DMF Media.
- [x] Mejorar la ayuda y documentación de los ajustes del plugin.
- [x] Compatibilidad con listas de reproducción públicas de YouTube mediante RSS.
- [x] Mejoras visuales de las tarjetas de vídeo.
- [x] Experiencia de usuario: estados de carga y errores del reproductor.
- [x] Permitir omitir los vídeos más recientes del feed mediante un atributo `offset`.
- [ ] Aceptar `@nombreCanal` además del Channel ID. Pospuesto: una implementación fiable requiere YouTube Data API v3 y una API key.
- [x] Mejorar accesibilidad del modal: foco, tabulación y retorno al elemento activador.

## v0.3 – Gutenberg y calidad

- [x] Bloque nativo de Gutenberg `dmf/media`, dinámico y con vista previa en el editor.
- [x] Inspector con origen (canal, playlist o vídeo), límite y offset.
- [x] Compatibilidad con contenedores y grids de GenerateBlocks.
- [x] Alineación ancha/completa, ancla y clase personalizada.
- [x] Suite de pruebas sin dependencias y `uninstall.php` con limpieza de opciones y transients.
- [ ] Atributos de presentación (relación de aspecto, estilos de tarjeta).
- [ ] Sistema de plantillas y hooks para desarrolladores.

## Futuro

- [ ] Vimeo y otras fuentes multimedia.
