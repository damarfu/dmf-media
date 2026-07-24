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
│   ├── Cache/
│   ├── Core/
│   ├── Feed/
│   ├── Models/
│   ├── Render/
│   └── Shortcodes/
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
                     Feed\YoutubeFeed
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

- YouTube RSS

En futuras versiones:

- Vimeo
- Podcasts
- Twitch
- RSS genérico

---

## Caché

`YoutubeFeed` utiliza la API Transients de WordPress para almacenar temporalmente el XML del feed y reducir las peticiones a YouTube.

La carpeta `Cache/` queda reservada para una posible capa de caché compartida cuando el plugin incorpore más proveedores multimedia.

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
- fecha de publicación
- descripción

---

## Renderer

Único responsable de generar HTML.

No obtiene datos.

No accede a WordPress.

Simplemente recibe un array de objetos Video y produce el marcado.

---

## Shortcodes

Punto de entrada desde WordPress.

Responsabilidades:

- leer atributos
- solicitar vídeos
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
