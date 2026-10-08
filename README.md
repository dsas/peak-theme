# Peak

A warm, textured block theme for deansas.org (writing) and photos.deansas.org (photos). Requires WordPress 6.7+ and PHP 8.1+.

## Writing and photos

Posts with the **Image** or **Gallery** post format are photo posts. Everything else is writing. The theme routes them itself:

- A photo post uses the **Photo post** template (`single-photo`): the featured image full width (or the first image, if there's no featured image), then any gallery in justified rows.
- An archive with no writing in it (a tag, category or date that only has photos) uses **Photo archive** (`photos-archive`), a justified grid.
- Writing archives, the timeline and "More from…" leave photo posts out, and older/newer links never cross between the two kinds.
- The **Photos grid** template is chosen per page; it's the front page of photos.deansas.org.

Both sites run the same theme, so they can merge later without a redesign.

## Writing tips

- **Block styles:** Boxout (on Group) for asides, Timeline (on List; start each item with bold text for its date) and Emoji list (on List; start each item with an emoji).
- **Patterns** (in the Peak category): About: who I am, About: favourites, Colophon, Home intro, Timeline.
- **Galleries:** use the core Gallery block. In photo posts it becomes wide justified rows; Jetpack's tiled gallery keeps its own layout.
- **Featured images on writing posts:** landscape and square images get the title over the bottom of the image; portrait images keep the title above. Recommended: 16:9, at least 2200px wide, subject near the centre (crops are taken from the middle) and the bottom-left fairly quiet (the title sits there).
- **Untitled posts** (old asides) are fine: the theme uses their first few words wherever a title is needed.

## Colourways

Peak (light) and Dusk (dark) are switchable by visitors. Without a stored choice, the visitor's system setting decides.

Edit colourways in `theme.json` and `styles/*.json`, not in Site Editor → Styles. The switcher reads the theme files, so Styles panel colour edits are overridden once a visitor picks a colourway or has a dark system setting. In particular, don't pick Dusk in the Styles panel: it makes Dusk the base colours, and light-mode visitors then see Dusk with a "Switch to Dusk" toggle that does nothing on its first click.

## Code layout

- `inc/lib/`: pure PHP functions with no WordPress calls, covered by the PHPUnit tests.
- `inc/hooks/`: the WordPress wiring (filters and actions). Both folders are loaded automatically by `functions.php`.
- `assets/css/`: one stylesheet per area, all enqueued automatically on the front end and in the editor.
- `assets/js/rider.js`: the cyclist who rides along the homepage hills once when the page loads (homepage only).
- `blocks/`: the theme's blocks (Writing timeline, Year rail, Style switcher), each registered from its `block.json`. They render on the server; `blocks/editor.js` gives them a live preview and settings in the editor.
- `templates/`, `parts/`, `patterns/`, `styles/`: as in any block theme.

No build step: edit and reload.

## Local development

```
composer install
composer test        # PHPUnit suite for the pure logic in inc/lib/ (PHPUnit 12 needs PHP 8.3+)
bin/build-zip.sh     # builds dist/peak.zip from the committed HEAD
```

The zip leaves out development files (tests, `bin/`, Composer files, this README); see `.gitattributes`.

## Deploying

Upload `dist/peak.zip` (Appearance → Themes → Add New → Upload), or connect this repository with WordPress.com GitHub Deployments, deploying to `wp-content/themes/peak`.
