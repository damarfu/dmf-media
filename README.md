# DMF Media

Lightweight WordPress plugin to display YouTube channel videos using the official RSS feed.

DMF Media is designed to integrate perfectly with GeneratePress and GenerateBlocks, providing clean HTML and letting your theme control the layout.

---

## Features

- Displays the latest videos from a YouTube channel.
- Displays a specific YouTube video by ID or URL.
- Uses the official YouTube RSS feed.
- Lightweight cache system.
- Responsive layout.
- Integrated modal video player.
- Autoplay on click.
- ESC key and click outside to close.
- Shortcode support.
- Minimal CSS, easy to customize.

---

## Requirements

- WordPress 6.7+
- PHP 8.1+

---

## Installation

1. Upload the plugin.
2. Activate it.
3. Go to **Settings → DMF Media**.
4. Enter your YouTube Channel ID.
5. Save changes.

---

## Shortcode

Default:

```text
[dmf_media]
```

Latest video:

```text
[dmf_media limit="1"]
```

Three videos:

```text
[dmf_media limit="3"]
```

Different channel:

```text
[dmf_media channel="UCxxxxxxxxxxxxxxxx"]
```

Specific video:

```text
[dmf_media video="VIDEO_ID"]
```

Specific video using a YouTube URL:

```text
[dmf_media video="https://www.youtube.com/watch?v=VIDEO_ID"]
```

The `video` attribute displays one specific video. When it is used, it takes priority over the `channel` and `limit` attributes.

---

## Philosophy

DMF Media focuses on content, not layout.

The plugin provides:

- Video data
- Thumbnails
- Titles
- Player

Your WordPress theme or block builder is responsible for the design.

---

## Roadmap

### v0.1

- ✅ RSS Feed
- ✅ Cache
- ✅ Modal player
- ✅ Responsive layout
- ✅ Shortcodes

### v0.2

- ✅ Manual video by ID or YouTube URL
- ⏳ Support `@channel`
- ⏳ Gutenberg block
- ⏳ Playlist support
- ⏳ Vimeo support

---

## License

GPL-2.0-or-later
