<?php declare(strict_types=1);

use Kirby\CLI\CLI;
use Kirby\Cms\File;
use Kirby\Cms\Page;
use PresProg\AutoFileTemplates\AutoFileTemplates;
use PresProg\AutoFileTemplates\PluginOptions;

return [
    'name' => 'auto-templates',
    'args' => [
      'force' => [
          'prefix' => 'f',
          'longPrefix' => 'force',
          'noValue' => true,
          'castTo' => 'bool',
      ],
    ],
    'command' => function (CLI $cli) {
        $kirby = $cli->kirby();

        // Virtual admin user
        $kirby->impersonate('kirby');

        $options = $kirby->options();

        if ($cli->arg('force') === true) {
            $options['presprog.auto-file-templates']['forceOverwrite'] = true;
        }

        $autoTemplates = new AutoFileTemplates($kirby, PluginOptions::createFromOptions($options));

        /** @var Page $page */
        foreach ($kirby->site()->index() as $page) {
            /** @var File $file */
            foreach ($page->files() as $file) {
                $template = $autoTemplates->autoAssign($file);

                if (!$template) {
                    continue;
                }

                $cli->climate()->out(sprintf(
                    '<green>%s</green>: %s',
                    $file->filename(),
                    $template,
                ));
            }
        }

        $cli->climate()->out('All files updated');
    },
];
