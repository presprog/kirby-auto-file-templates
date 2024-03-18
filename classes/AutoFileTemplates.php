<?php declare(strict_types=1);

namespace PresProg\AutoFileTemplates;

use Kirby\Cms\App;
use Kirby\Cms\Blueprint;
use Kirby\Cms\File;
use Kirby\Filesystem\F;

readonly class AutoFileTemplates
{
    public function __construct(
        private App $kirby,
        private PluginOptions $options,
    ) {}

    public function getTemplateFromType(File $file): ?string
    {
        if ($this->options->autoAssign === false) {
            return null;
        }

        // Virtual admin user
        $this->kirby->impersonate('kirby');

        $template = $file->type();

        if (!$this->typeExists($template)) {
            return null;
        }

        if (!$this->templateExists($template)) {
            return null;
        }

        $file->update(['template' => $template]);
        $file->save();

        return $template;
    }

    private function typeExists(?string $template): bool
    {
        return array_key_exists($template, F::$types);
    }

    private function templateExists(?string $template): bool
    {
        $blueprints = $this->kirby->blueprints('files');
        return in_array($template, $blueprints, true);
    }


}
