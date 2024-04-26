![Kirby Auto File Templates Plugin](/.github/banner.png)

# Automatically assign templates to your uploaded files

> ⚡ Ready for Kirby 4!

This plugin automatically assigns file templates to your uploaded files, based on the respective file type. This way it does not matter, which file type you upload from which field or section – the template assigned will always be the same. This is especially handy, when you use a single files section per page, that stores all the different file types used on the page.

> [!IMPORTANT]
> Requires at least Kirby 4.0 and PHP 8.2

## 🚀 How to use

Set up your configuration (see next section) first. The plugin will then run after each uploaded file (`file.create:after` hook) and assign the configured template automatically.

If you add the plugin to an existing project, you can run the `auto-templates` command from the CLI. It will iterate over every file in every page and assign the template according to the configuration:

```bash
$ php vendor/bin/kirby auto-templates

image.png: image
video.mp4: video

> All files updated
```

By default, existing template assignments will not be touched. To change that, run the command with `--force/-f` or set the `forceOverwrite` option globally in your `config.php` (see below).

## ⚙️ Config

The plugin works in an opt-in manner: It does nothing except you tell it to.

```php
// site/config/config.php

'presprog/auto-file-templates' => [
  // Do nothing (default)
  'auto-assign' => false,

  // OR automatically assign a file template for every file type
  'auto-assign' => true,

  // OR only assign templates to some file types (ignore file types other than `image` and `video`
  'auto-assign' => [
      'image' => true,
      'video' => true,
  ],

  // OR define a specific template for just some file types and let the plugin decide for the others
  'auto-assign' => [
      'image' => 'my-custom-image-blueprint',
      'video' => true, // => 'video'
  ],

  // OR handle more advanced use-cases in callable yourself (assign different file templates for vector and raster images)
  'auto-assign' => [
      'image' => function(\Kirby\Cms\File $file) {
          return match (F::extension($file->filename())) {
              'svg'   => 'vector',
              default => 'image',
          };
      },
  ],

  // Overwrite existing template assignments (default: false)
  'forceOverwrite' => true,
],

```

With `auto-assign = true`, each file type will get the identically named file blueprint assigned as template:

| File type | File blueprint                  |
|-----------|---------------------------------|
| audio     | site/blueprints/files/audio     |
| archive   | site/blueprints/files/archive   |
| code      | site/blueprints/files/code      |
| image     | site/blueprints/files/image     |
| video     | site/blueprints/files/video     |
| your-type | site/blueprints/files/your-type |

\* The Kirby core file types `archive`, `audio`, `document`,  `image` and `video` and [your custom file type extensions](https://getkirby.com/docs/reference/plugins/extensions/file-types) are supported out-of-the-box.

## 💻 How to install

Install this plugin via **Composer**:

```bash
composer require presprog/kirby-auto-file-templates
```

Or **download the ZIP file** from GitHub and unpack it to `site/plugins/kirby-auto-file-templates`

## ✅ To do
* [ ] Add multi-language support

## 📄 License

MIT License Copyright © 2024 Present Progressive

----

<img src="/logo.svg?raw=true" width="200" height="43">

Made by [Present Progressive](https://www.presentprogressive.de) for the Kirby community.
