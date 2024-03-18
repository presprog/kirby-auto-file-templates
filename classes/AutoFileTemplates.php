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

    public function autoAssign(File $file): ?string
    {
        if ($this->options->autoAssign === false) {
            return null;
        }

        // Virtual admin user
        $this->kirby->impersonate('kirby');

        if ($template = $this->getTemplateForFile($file)) {
            $file->update(['template' => $template]);
            $file->save();
        }

        return $template;
    }

    private function getTemplateForFile(File $file): ?string
    {
        if (($templates = $this->options->templates) && \array_key_exists($file->type(), $templates)) {
            $template = $templates[$file->type()];

            if (\is_callable($template)) {
                $template = $template($file);
            }

            if (\is_string($template) || \is_null($template)) {
                return $template;
            }

            return null;
        }

        $template = $file->type();

        if (!$this->typeExists($template)) {
            return null;
        }

        if (!$this->templateExists($template)) {
            return null;
        }

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
