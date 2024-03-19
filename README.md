# Automatic file templates for your file uploads

…

## Config

The plugin works in an opt-in manner: It does not do anything except you tell it to.

```php
// site/config/config.php

// Do nothing (default)
'auto-assign' => false,

// Automatically assign a file template for every file type (`image`
'auto-assign' => true,

// Only assign templates to some file types (ignore file types other than `image` and `video`
'auto-assign' => [
    'image' => true,
    'video' => true,
],

// Handle more advanced use-cases in callable yourself (assign different file templates for vector and raster images)
'auto-assign' => [
    'image' => function(\Kirby\Cms\File $file) {
        return match (F::extension($file->filename())) {
            'svg'   => 'vector',
            default => 'image',
        };
    },
],

```

With `auto-assign = true`, each file type will get the identically named file blueprint assigned as template:

| File type | File blueprint |
|-----------|----------------|
| audio     | audio          |
| archive   | archive        |
| code      | code           |
| image     | image          |
| video     | video          |
| ...*      | ...*           |

* Manually added file types work out of the box, too.

## Installation

Install this plugin via **Composer**:

```bash
composer require presprog/kirby-auto-file-templates
```

Or **download the ZIP file** from GitHub and unpack it to `site/plugins/kirby-auto-file-templates`

## License

MIT License Copyright © 2024 Present Progressive

----

<img src="/logo.svg?raw=true" width="200" height="43">

Made by [Present Progressive](https://www.presentprogressive.de) for the Kirby community.
