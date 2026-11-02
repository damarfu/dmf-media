# DMF Media Architecture

## Objetivo

DMF Media es un plugin de WordPress cuyo objetivo es mostrar contenido multimedia procedente de plataformas externas, comenzando por YouTube.

La filosofía del proyecto es mantener una arquitectura modular, desacoplada y fácilmente extensible para soportar nuevas fuentes de contenido en futuras versiones.

---

# Estructura del proyecto

```
dmf-media/
│
├── assets/
│   ├── css/
│   ├── js/
│   └── images/
│
├── languages/
│
├── src/
│   ├── Admin/
│   ├── Core/
│   ├── Feed/
│   ├── Models/
│   ├── Render/
│   ├── Resolver/
│   ├── Shortcodes/
│   └── Support/
│
├── vendor/
│
├── composer.json
├── dmf-media.php
└── README.md
```

---

# Flujo de ejecución

```
WordPress
      │
      ▼
dmf-media.php
      │
      ▼
Core\Plugin
      │
      ├─────────────► Admin\Settings
      │
      └─────────────► Shortcodes\MediaShortcode
                              │
                              ▼
                     Resolver\MediaResolver ─────► Support\YoutubeId
                              │
              ┌───────────────┼───────────────┐
              ▼               ▼               ▼
      Feed\YoutubeFeed  Feed\YoutubePlaylist  Feed\YoutubeVideo
              └───────────────┼───────────────┘
                              │
                              ▼
                     Models\Video[]
                              │
                              ▼
                          Renderer
                              │
                              ▼
                           HTML
```

---

# Componentes

## Core

Inicializa el plugin.

Responsabilidades:

- carga del autoloader
- registro de hooks
- arranque del plugin

---

## Admin

Contiene toda la configuración accesible desde el panel de administración.

Actualmente:

- Channel ID
- Número máximo de vídeos
- Ayuda del shortcode

---

## Feed

Responsable únicamente de obtener información desde plataformas externas.

Actualmente implementado:

- `YoutubeFeed`: RSS público de un canal.
- `YoutubePlaylist`: RSS público de una playlist.
- `YoutubeVideo`: metadatos de un vídeo mediante oEmbed.
- `YoutubeRssParser`: conversión del XML en objetos `Video`.

En futuras versiones:

- Vimeo
- Podcasts
- Twitch
- RSS genérico

---

## Resolver

`MediaResolver` decide qué fuente se muestra (prioridad `video` > `playlist` > `channel`),
valida los identificadores y aplica `limit` y `offset`. Devuelve un `MediaResult` con los
vídeos o con un mensaje de error ya redactado, de forma que el shortcode y el futuro
bloque de Gutenberg compartan exactamente la misma lógica.

`Support\YoutubeId` centraliza la normalización de Channel ID, Playlist ID y vídeo a
partir de un identificador o de una URL de YouTube.

---

## Caché

Cada clase de `Feed` utiliza la API Transients de WordPress para almacenar temporalmente
la respuesta de YouTube y reducir las peticiones:

- Éxito: 30 minutos de caché del XML o de los metadatos.
- Fallo: 5 minutos de caché negativa, para no repetir una petición que acaba de fallar.

Solo se guarda una respuesta si el código HTTP es 200, el `Content-Type` es el esperado y
el contenido se puede interpretar. Así se evita cachear durante media hora una página de
error o un aviso de consentimiento.

---

## Models

Representa los objetos de negocio.

Actualmente:

Video

Propiedades:

- título
- ID de YouTube
- URL
- miniatura
- fecha de publicación (puede ser `null` en el vídeo manual)
- descripción

---

## Renderer

Único responsable de generar HTML.

No obtiene datos ni consulta opciones.

Recibe un array de objetos Video y produce el marcado, escapando cada valor con las
funciones de WordPress.

El modal de reproducción no forma parte de ese HTML. `Modal` lo imprime una sola vez
desde `wp_footer`, como hijo directo de `<body>`, para que ningún contenedor del tema
pueda recortarlo con `overflow` o atraparlo en su contexto de apilamiento. El shortcode
solo marca que hace falta mediante `Modal::require()`.

---

## Shortcodes

Punto de entrada desde WordPress.

Responsabilidades:

- leer atributos
- delegar en `MediaResolver`
- enviar el resultado al Renderer

---

# Principios de diseño

El proyecto sigue varios principios de ingeniería de software.

## Responsabilidad única

Cada clase tiene una única responsabilidad.

---

## Bajo acoplamiento

Los componentes dependen lo mínimo posible entre sí.

---

## Alta cohesión

Cada carpeta agrupa funcionalidades relacionadas.

---

## Extensibilidad

Las futuras plataformas deberán añadirse implementando nuevas clases dentro de Feed sin modificar el resto del sistema.

---

# Roadmap técnico

## Próximo

- soporte para @usuario
- listas de reproducción
- mejoras de accesibilidad
- Privacy Enhanced Mode

## Futuro

- Vimeo
- Twitch
- TikTok
- Instagram
- múltiples proveedores multimedia

---

# Convenciones

- PSR-4
- Composer
- PHP 8.1+
- WordPress Coding Standards (cuando sea posible)
- Escape de salida y sanitización de entrada
- Arquitectura modular

---

# Filosofía

DMF Media pretende ser un plugin sencillo para el usuario, pero sólido internamente.

La prioridad es mantener un código limpio, mantenible y preparado para crecer sin necesidad de grandes refactorizaciones.
