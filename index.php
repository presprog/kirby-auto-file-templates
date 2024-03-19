<?php declare(strict_types=1);

use Kirby\Cms\App;
use Kirby\Cms\File;
use PresProg\AutoFileTemplates\AutoFileTemplates;
use PresProg\AutoFileTemplates\PluginOptions;

@include_once __DIR__ . '/vendor/autoload.php';

App::plugin('presprog/auto-file-templates', [
    'options' => [
        'autoAssign' => true,
    ],
    'commands' => [
        'auto-templates' => require __DIR__ . '/extensions/commands/auto-templates.php',
    ],
    'hooks' => [
        'file-create:after' => function (File $file) {
            $options = PluginOptions::createFromOptions(kirby()->options());
            return (new AutoFileTemplates(kirby(), $options))->autoAssign($file);
        }
    ]
]);
