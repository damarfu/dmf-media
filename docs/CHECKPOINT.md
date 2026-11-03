# Punto de Control Técnico — DMF Media 0.3.0

> Documento vivo. Actualizar al cerrar cada fase o antes de una pausa larga.
>
> Fecha: 2026-11-03 · Repo: `damarfu/dmf-media` · Versión: `0.3.0`

---

## 1. Estructura de archivos actual

```text
dmf-media-dsh/                     # en producción debe llamarse dmf-media/
├── dmf-media.php                  # cabecera, constantes, autoload, enlace Ajustes, boot
├── uninstall.php                  # limpieza al eliminar el plugin
├── LICENSE                        # GPL-2.0
├── README.md
├── CHANGELOG.md
├── ARCHITECTURE.md
├── ROADMAP.md
├── composer.json
├── composer.lock
├── .gitignore
│
├── assets/
│   ├── css/frontend.css
│   └── js/frontend.js
│
├── blocks/media/
│   ├── block.json
│   ├── index.js                   # editor, sin build
│   ├── render.php
│   └── editor.css
│
├── src/
│   ├── Admin/Settings.php
│   ├── Blocks/MediaBlock.php
│   ├── Core/Plugin.php
│   ├── Feed/
│   │   ├── YoutubeFeed.php
│   │   ├── YoutubePlaylist.php
│   │   ├── YoutubeRssParser.php
│   │   └── YoutubeVideo.php
│   ├── Models/Video.php
│   ├── Render/
│   │   ├── Modal.php
│   │   └── Renderer.php
│   ├── Resolver/
│   │   ├── MediaResolver.php
│   │   └── MediaResult.php
│   ├── Shortcodes/MediaShortcode.php
│   └── Support/YoutubeId.php
│
├── tests/
│   ├── bootstrap.php              # dobles de funciones WordPress
│   ├── run.php
│   ├── fixtures/feed.xml
│   ├── MediaBlockTest.php
│   ├── MediaResolverTest.php
│   ├── RendererTest.php
│   ├── SettingsTest.php
│   ├── UninstallTest.php
│   ├── YoutubeIdTest.php
│   └── YoutubeRssParserTest.php
│
├── docs/CHECKPOINT.md             # este documento
│
├── vendor/                        # Composer, versionado
│
├── dist/                          # SOLO LOCAL, ignorado en git
│   ├── dmf-media-0.3.0.zip
│   └── release-notes-v0.3.0.md
│
└── .git-bak/                      # SOLO LOCAL, copia del .git, ignorada
```

Notas:

- `assets/images/` y `languages/` aparecen en `ARCHITECTURE.md`, pero **no existen** en el proyecto actual. Es deuda de documentación.
- `dist/` y `.git-bak/` no forman parte del plugin desplegable.
- El slug de producción debe ser **`dmf-media`**, no `dmf-media-dsh` ni `dmf-media-main`.

---

## 2. Funciones 100% terminadas y operativas

### Fuentes de contenido

| Función | Estado | Detalle |
|---|---|---|
| Canal por RSS | ✅ | `YoutubeFeed`; máximo real de 15 vídeos |
| Playlist por RSS | ✅ | `YoutubePlaylist`; `PL`, `UU`, `LL`, `FL`, `RD`, `OLAK5uy_` |
| Vídeo manual por oEmbed | ✅ | `YoutubeVideo`; ID, `watch`, `youtu.be`, `shorts`, `embed`, `live`, `m.youtube.com` |
| Prioridad de fuentes | ✅ | `video` > `playlist` > `channel` |
| `limit` | ✅ | 1–15; `0` en el bloque usa el ajuste del plugin |
| `offset` | ✅ | Acepta string (shortcode) e int (bloque); se aplica antes de `limit` |
| Validación de IDs/URLs | ✅ | `YoutubeId` para canal, playlist y vídeo |
| Caché de éxito | ✅ | 30 min (`1800 s`) para XML y metadatos |
| Caché negativa | ✅ | 5 min (`300 s`) para no repetir fallos |
| Validación HTTP/Content-Type/XML | ✅ | No se cachean páginas de error |

### Administración

| Función | Estado |
|---|---|
| Página Ajustes → DMF Media | ✅ |
| Canal por ID o URL `/channel/UC...` | ✅ |
| Límite 1–15 | ✅ |
| Sanitización y conservación del canal anterior si es inválido | ✅ |
| Avisos con `add_settings_error` | ✅ |
| Ayuda integrada de shortcodes y bloque | ✅ |

### Shortcode y bloque

| Función | Estado |
|---|---|
| `[dmf_media]` con `channel`, `limit`, `offset`, `video`, `playlist` | ✅ |
| Bloque dinámico `dmf/media` | ✅ |
| Inspector: origen, canal, playlist, vídeo, límite, offset | ✅ |
| Vista previa con `ServerSideRender` | ✅ |
| Alineación ancha/completa, ancla y clase personalizada | ✅ |
| Aviso si no hay canal configurado | ✅ |
| Estilos del editor dentro del iframe | ✅ |

### Frontend

| Función | Estado |
|---|---|
| Modal global en `wp_footer`, una sola vez | ✅ |
| Delegación de clics (contenido AJAX) | ✅ |
| `readyState` para carga diferida | ✅ |
| Timeout de 8 s y estado de error | ✅ |
| `inert` en el fondo + compensación de scroll | ✅ |
| Foco atrapado, ESC y retorno del foco | ✅ |
| CSS responsive, `focus-visible`, `prefers-reduced-motion` | ✅ |
| Carga condicional de assets | ✅ |
| `alt=""` en miniaturas | ✅ |

### Calidad e infraestructura

| Función | Estado |
|---|---|
| Autoload PSR-4 de Composer | ✅ |
| `uninstall.php` (opción + transients) | ✅ |
| Suite de pruebas sin dependencias | ✅ **34 pruebas, 107 aserciones, 0 fallos** |
| `composer test` | ✅ |
| Documentación README/CHANGELOG/ARCHITECTURE/ROADMAP | ✅ |
| Release pública `v0.3.0` | ✅ |

Comandos de verificación:

```bash
php tests/run.php
composer test
php -l dmf-media.php
```

### Estado Git/publicación

- `main`: por delante del tag (LICENSE, nota del README y este checkpoint).
- Tag `v0.3.0`: `6585a05` (commit de la release).
- Release `v0.3.0`: pública, *Latest*, con `dmf-media-0.3.0.zip`.
- Repositorio público: https://github.com/damarfu/dmf-media
- Licencia detectada por GitHub: GPL-2.0.
- `main` va por delante del tag (LICENSE, nota del README y este checkpoint). El ZIP de la release **no incluye LICENSE**; si quieres que lo incluya, hay que sacar 0.3.1 o reconstruir el asset.

---

## 3. Pendientes y bugs abiertos

### Bugs abiertos

**Ninguno conocido en 0.3.0.** El último detectado fue que `offset` entero del bloque se ignoraba; se corrigió en `MediaResolver::resolveOffset()` y está cubierto por test.

### Limitaciones conocidas (no son bugs)

1. **`@usuario` no soportado**: sin API key no es fiable. Requiere YouTube Data API v3 o scraping del HTML.
2. **Máximo 15 vídeos**: límite del RSS público. Para más, API v3.
3. **Privacy Enhanced Mode** (`youtube-nocookie`) y consentimiento: no implementado.
4. **Detección de embed bloqueado**: si el iframe carga una página de error, `load` se dispara y no es detectable por seguridad cross-origin. El timeout de 8 s solo cubre el caso de que el iframe no responda nunca.
5. **Vista previa del editor no interactiva**: por diseño; los clics están desactivados.
6. **`uninstall.php` borra ajustes y transients**: correcto para desinstalación limpia, pero peligroso si se actualiza borrando y reinstalando. No hay export/import de ajustes.
7. **Multisite**: `uninstall.php` limpia solo el sitio actual, no cada blog de la red.
8. **Sin i18n**: español fijo; no hay `.pot` ni `load_plugin_textdomain`.

### Backlog funcional

- Atributos de presentación del bloque (relación de aspecto, estilos de tarjeta).
- Hooks/plantillas para desarrolladores.
- Vimeo, Twitch, TikTok, Instagram.
- Exportar/importar ajustes.
- Casilla «borrar datos al desinstalar» o conservar ajustes por defecto.
- Más de 15 vídeos mediante API.
- `@handle` mediante API o scraping.

### Deuda técnica

- **CI**: no hay GitHub Actions ejecutando `php tests/run.php`.
- **PHPStan/PHPCS/PHPUnit**: la suite es un runner propio sin dependencias; no hay análisis estático.
- **Release drift**: `main` tiene LICENSE, nota del README y este checkpoint que no están en el ZIP de `v0.3.0`.
- **Documentación**: `ARCHITECTURE.md` menciona `assets/images/` y `languages/` inexistentes.
- **Entorno Local**: hay tres copias (`dmf-media-main` activa, `dmf-media-dsh` inactiva, `dmf-media` antigua) y los ajustes del sitio de pruebas se borraron al ejecutarse `uninstall.php`. No afecta a producción.
- **Handle duplicado**: `dmf-media-block-editor` se usa como handle de script y de estilo (legal, pero confuso).

---

## 4. Variables, hooks y base de datos que recordar

### Constantes

```php
DMF_MEDIA_VERSION  // '0.3.0'
DMF_MEDIA_FILE     // ruta absoluta a dmf-media.php
DMF_MEDIA_PATH     // ruta al plugin con barra final
```

Constantes internas:

```php
MediaResolver::DEFAULT_LIMIT = 6;
MediaResolver::MAX_LIMIT     = 15;

YoutubeId::VIDEO_PATTERN    = '/^[A-Za-z0-9_-]{11}$/';
YoutubeId::CHANNEL_PATTERN  = '/^UC[A-Za-z0-9_-]{22}$/';
YoutubeId::PLAYLIST_PATTERN = '/^[A-Za-z0-9_-]{13,64}$/';

YoutubeFeed::CACHE_TTL     = 1800;
YoutubeFeed::FAILURE_TTL   = 300;
// Igual en YoutubePlaylist y YoutubeVideo
```

### Hooks de WordPress

| Hook | Prioridad | Callback | Función |
|---|---|---|---|
| `init` | 10 | closure → `MediaShortcode::register()` | registra `[dmf_media]` |
| `init` | 10 | `Plugin::registerBlock` | handles de editor, `register_block_type()`, localize |
| `admin_menu` | 10 | `Settings::addMenu` | página de ajustes |
| `admin_init` | 10 | `Settings::registerSettings` | `register_setting` + campos |
| `wp_enqueue_scripts` | 10 | `Plugin::maybeEnqueueAssets` | assets condicionales |
| `wp_footer` | 10 | `Modal::render` | modal global, una vez |
| `plugin_action_links_{plugin_basename}` | 10 | closure | enlace «Ajustes» |

Registros internos:

```php
add_shortcode( 'dmf_media', [ MediaShortcode::class, 'render' ] );
register_block_type( DMF_MEDIA_PATH . 'blocks/media/block.json' );
register_setting( 'dmf_media_group', 'dmf_media_settings', [...] );
add_options_page( 'DMF Media', 'DMF Media', 'manage_options', 'dmf-media', [...] );
add_settings_section( 'dmf_media_main', ..., 'dmf-media' );
add_settings_field( 'channel', ..., 'dmf-media', 'dmf_media_main' );
add_settings_field( 'limit', ..., 'dmf-media', 'dmf_media_main' );
```

No hay:

- endpoints REST propios (el bloque usa el de core `/wp/v2/block-renderer/dmf/media`);
- `wp_ajax_*`;
- cron;
- rewrite rules;
- CPT ni taxonomías.

### Base de datos

**Opción:**

```php
'dmf_media_settings' => [
    'channel' => '',   // Channel ID validado; vacío = sin configurar
    'limit'   => 6,    // entero 1..15
]
```

**Transients:**

| Clave | Valor | TTL |
|---|---|---|
| `dmf_media_feed_v2_` + `md5(channelId)` | XML del canal | 1800 s |
| `dmf_media_feed_fail_v1_` + `md5(channelId)` | `'1'` | 300 s |
| `dmf_media_playlist_v1_` + `md5(playlistId)` | XML de playlist | 1800 s |
| `dmf_media_playlist_fail_v1_` + `md5(playlistId)` | `'1'` | 300 s |
| `dmf_media_video_v1_` + `md5(videoId)` | `['title','thumbnail']` | 1800 s |
| `dmf_media_video_fail_v1_` + `md5(videoId)` | `'1'` | 300 s |

Cada transient de éxito tiene su `_transient_timeout_*` asociado.

**Contenido en posts:**

```text
[dmf_media channel="UC..." limit="6" offset="0"]
[dmf_media playlist="PL..." limit="6"]
[dmf_media video="https://youtu.be/..."]

<!-- wp:dmf/media {"source":"channel","channel":"UC...","limit":0,"offset":0} /-->
```

Atributos del bloque: `source` (`default|channel|playlist|video`), `channel`, `playlist`, `video`, `limit`, `offset`; más los de soportes: `align`, `className`, `anchor`, `style`, `lock`, `metadata`, `gbBlockCondition`, `gbBlockConditionInvert`.

**Uninstall:**

```php
delete_option( 'dmf_media_settings' );
// borra todos los _transient_dmf_media_* del sitio actual
```

No borra posts, páginas ni bloques. Solo actúa al **eliminar** el plugin, no al desactivar ni actualizar.

### Estado global / runtime

- `Modal::$needed` y `Modal::$printed` (static): evitan modal duplicado.
- `$GLOBALS['wpdb']`: usado solo en `uninstall.php`.
- `get_post()` / global `$post`: usado por `Plugin::currentPageUsesMedia()`.
- `window.dmfMediaBlock`: datos localizados para el editor (`name`, `title`, `attributes`, `supports`, `hasDefaultChannel`).
- `window.wp.*`: `wp.blocks`, `wp.element`, `wp.components`, `wp.blockEditor`, `wp.serverSideRender`.

### Handles de assets

| Handle | Tipo | Uso |
|---|---|---|
| `dmf-media` | style | frontend |
| `dmf-media` | script | frontend, footer |
| `dmf-media-block-editor` | script | editor |
| `dmf-media-block-editor` | style | CSS frontend dentro del editor |
| `dmf-media-block-editor-preview` | style | `editor.css` (preview no interactiva) |

### DOM / CSS que no conviene renombrar

- IDs: `dmf-video-modal`, `dmf-video-player`.
- Clases: `dmf-media-grid`, `dmf-media-single`, `dmf-media-message`, `dmf-video-card`, `dmf-video-link`, `dmf-video-thumb`, `dmf-video-overlay`, `dmf-video-play`, `dmf-video-title`, `dmf-video-modal__overlay`, `dmf-video-modal__dialog`, `dmf-video-modal__close`, `dmf-video-player--loading`, `dmf-video-player--ready`, `dmf-video-player--error`, `dmf-video-player__status`, `dmf-video-player__spinner`, `dmf-video-player__close-error`.
- `body.dmf-modal-open`.
- `wp-block-dmf-media`, `dmf-media-block`, `dmf-media-block__preview`, `dmf-media-block__loading`.
- `data-video-id` en `.dmf-video-link`.

---

## 5. Entorno actual

- **Local**: `http://damarfu-pruebas.local`, WP 7.1, PHP 8.2, GeneratePress 3.6.1, GenerateBlocks 2.5.0 + Pro 2.8.0.
- Plugins instalados ahí:
  - `dmf-media-main` 0.3.0 (ZIP de código fuente, **activo**),
  - `dmf-media-dsh` 0.3.0 (symlink al workspace, inactivo),
  - `dmf-media` 0.2.3 (copia antigua, inactivo).
- Los ajustes del sitio de pruebas (`dmf_media_settings`) están **borrados**; la web de producción que probaste funciona correctamente.
- Producción: plugin instalado y validado; configuración y diseño intactos.

---

## 6. Siguiente paso recomendado

Orden sugerido al retomar:

1. **Decidir qué hacer con `uninstall.php`**: conservar ajustes por defecto o añadir export/import. Es el mayor riesgo operativo actual.
2. **Publicar 0.3.1** si quieres que el ZIP de la release incluya `LICENSE` y la nota del README; ahora `main` va por delante del tag.
3. **GitHub Actions** con `php tests/run.php`.
4. **Atributos de presentación del bloque** (relación de aspecto, estilos de tarjeta).
5. **Privacy Enhanced Mode** (`youtube-nocookie`) y consentimiento.
6. Limpiar el entorno Local: dejar una sola copia activa y recuperar los ajustes de prueba.

Con esto puedes retomar sin depender del historial de la conversación.
