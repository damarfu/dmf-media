# DMF Media

Lightweight WordPress plugin to display YouTube channel videos using the official RSS feed.

DMF Media is designed to integrate perfectly with GeneratePress and GenerateBlocks, providing clean HTML and letting your theme control the layout.

---

## Features

- Displays the latest videos from a YouTube channel.
- Displays videos from a public YouTube playlist.
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
4. Configure your YouTube Channel ID and default video limit.
5. Save changes.

---

## Configuration

### Channel ID

The Channel ID is the unique identifier for a YouTube channel. It starts with `UC`, for example:

```text
UCfoi0nD0pW_ipZOldgTrp4A
```

You can find it in a YouTube Studio or channel URL that contains `/channel/UC...`.

YouTube handles such as `@channelname` are not supported as a Channel ID yet.

### Default video limit

The default limit determines how many channel videos DMF Media displays when the shortcode does not include a `limit` attribute. The valid range is 1–20 videos.

---

## Shortcodes

### Channel feed

Use the configured Channel ID and default limit:

```text
[dmf_media]
```

Override the number of videos for one shortcode, from 1 to 20:

```text
[dmf_media limit="3"]
```

Omitir los vídeos más recientes disponibles en el feed RSS antes de aplicar el límite:

```text
[dmf_media offset="1" limit="5"]
[dmf_media offset="2" limit="5"]
```

`offset="1"` omite el vídeo más reciente; `offset="2"` omite los dos más recientes. Después se aplica `limit` para determinar cuántos vídeos se muestran. Este atributo funciona con feeds de canal y playlists, pero solo puede trabajar con los vídeos que YouTube proporciona mediante RSS. En las playlists públicas, el RSS proporciona actualmente un máximo de 15 vídeos. No se aplica al modo de vídeo manual.

Use another Channel ID without changing the default configuration:

```text
[dmf_media channel="UCxxxxxxxxxxxxxxxx"]
```

### YouTube playlist

Display videos from a public playlist by Playlist ID:

```text
[dmf_media playlist="PLxxxxxxxx"]
```

You can also use a standard playlist URL:

```text
[dmf_media playlist="https://www.youtube.com/playlist?list=PLxxxxxxxx"]
```

The `limit` attribute applies to playlists too. If it is omitted, DMF Media uses the configured default limit:

```text
[dmf_media playlist="PLxxxxxxxx" limit="6"]
```

DMF Media reads public playlists through the YouTube RSS feed, so no API key is required. YouTube currently provides a maximum of 15 videos per playlist through this feed. The `limit` attribute can reduce that number, but cannot display more videos than YouTube provides.

### Manual video

Display one specific video by ID:

```text
[dmf_media video="VIDEO_ID"]
```

Display one specific video with a YouTube URL:

```text
[dmf_media video="https://www.youtube.com/watch?v=VIDEO_ID"]
```

The `video` attribute also supports `youtu.be` and `shorts` URLs.

### Source priority

DMF Media uses sources in this order: `video` > `playlist` > `channel`.

When `video` is used, DMF Media displays only that video and ignores the `playlist`, `channel` and `limit` attributes:

```text
[dmf_media video="VIDEO_ID" playlist="PLxxxxxxxx" channel="UCxxxxxxxx" limit="5"]
```

When `playlist` is used without `video`, DMF Media displays the playlist and ignores `channel`.

When `offset` is used, DMF Media skips that number of the most recent videos available in the YouTube RSS feed before applying `limit`:

```text
[dmf_media offset="1" limit="5"]
```

For public playlists, YouTube currently provides a maximum of 15 videos through RSS. `offset` and `limit` can only work with those available videos.

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
- ✅ YouTube playlist support
- ⏳ Support `@channel`
- ⏳ Gutenberg block
- ⏳ Vimeo support

---

## License

GPL-2.0-or-later
