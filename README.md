# Photography portfolio — photography.yasine.org

Editorial-style photography site: a vertical name list on the left, a large
cross-fading photo on the right, for both **People** and **Events**. Clicking
a name opens that person's/event's own gallery page. The top bar has
**People** and **Events** menus with a sub-menu per name, generated
automatically.

## How the site works

There is no CMS and no build step. The site is plain PHP + HTML/CSS/JS, and
every person/event is just **a folder of photos**. The pages scan the
`content/` folder on every request, so publishing is just a file upload.

```
content/
  people/
    jane-doe/
      cover.jpg       (optional — otherwise the first photo alphabetically is used)
      01.jpg
      02.jpg
      meta.json        (optional)
    john-smith/
      01.jpg
      ...
  events/
    berlin-2026/
      01.jpg
      ...
```

## Adding a new person or event — no code required

1. Connect to the site via Hostinger's **File Manager** (hPanel → Files →
   File Manager) or an FTP client, and go to `content/people/` (or
   `content/events/`).
2. Create a new folder. The folder name becomes the URL slug, e.g.
   `content/people/jane-doe/` → `photography.yasine.org/people/jane-doe/`.
   By default the display name is the folder name with dashes turned into
   spaces and each word capitalized ("jane-doe" → "Jane Doe") — add a
   `meta.json` (below) to override it.
3. Upload photos into that folder (`.jpg`, `.jpeg`, `.png` or `.webp`).
   That's it — the person/event now appears in the top menu, on the
   homepage, and has its own gallery page.

## Adding/removing photos from an existing gallery

Just upload or delete image files inside that folder. Nothing else needs to
change — the gallery grid and the thumbnails update automatically. Large
photos are fine to upload as-is: the server generates and caches resized
versions on first view (`thumb.php`), so you never need to resize images
yourself before uploading.

## Controlling name, order, bio and captions — `meta.json`

Drop an optional `meta.json` file inside a person/event folder:

```json
{
  "name": "Jane Doe",
  "order": 1,
  "bio": "Short one-line bio or tagline shown on the gallery page.",
  "cover": "02.jpg",
  "captions": {
    "03.jpg": "Rooftop session, Berlin"
  }
}
```

- `name` — display name (otherwise derived from the folder name).
- `order` — lower numbers appear first; entries without `order` are sorted
  alphabetically after the ordered ones.
- `cover` — which photo to use as the homepage hero image (otherwise the
  first photo alphabetically, or a file literally named `cover.jpg`).
- `captions` — optional per-photo captions shown on hover in the gallery.

None of these fields are required — an empty folder with just photos in it
already works.

## Removing a person or event

Delete (or rename) its folder under `content/people/` or `content/events/`.
It disappears from the menu and homepage immediately.

## Local development

Requires PHP 8.1+ with the GD extension (both available on the Hostinger
plan this is deployed to).

```
php -S localhost:8000
```

Then open `http://localhost:8000`.

## Deployment

The site is deployed directly to the Hostinger subdomain
`photography.yasine.org` (document root:
`/home/u997731848/domains/yasine.org/public_html/photography`). It does not
use a GitHub Actions pipeline — deploy by uploading the contents of this
repository (excluding `cache/`) to that directory, e.g. via Hostinger's
File Manager, FTP, or the Hostinger API's static-site-archive deploy
endpoint. `content/` and `cache/` on the server are left untouched by
redeploys of the code — only re-upload those if you're intentionally
changing content.

## Design tokens

Colors and fonts are defined as CSS custom properties at the top of
`assets/css/style.css` (`:root { ... }`) — change them there to reskin the
whole site without touching layout code.
