# Peak

A warm, textured block theme for deansas.org (writing) and photos.deansas.org (photos). Requires WordPress 6.7+ and PHP 8.1+.

## Local development

```
composer install
composer test        # PHPUnit suite for the pure logic in inc/lib/
bin/build-zip.sh     # builds dist/peak.zip from the committed HEAD
```

## Colourways

Edit colourways in theme.json / styles/*.json, not in Site Editor → Styles: the visitor switcher reads the theme files, so Global Styles colour edits are overridden once a visitor picks a style or uses a dark system setting.
