<?php declare(strict_types=1);

namespace PresProg\AutoFileTemplates;

use Kirby\Cms\App;
use Kirby\Cms\File;
use Kirby\Filesystem\F;

readonly class AutoFileTemplates
{
    private array $templateMap;

    public function __construct(
        private App $kirby,
        private PluginOptions $options,
    ) {
        $this->templateMap = $this->getTemplateMap($options->autoAssign);
    }

    public function autoAssign(File $file): ?string
    {
        if ($this->options->autoAssign === false) {
            return null;
        }

        // Do not overwrite existing templates
        if ($this->options->forceOverwrite === false && $file->template() !== 'default') {
            return null;
        }

        // Virtual admin user
        $this->kirby->impersonate('kirby');

        if (($template = $this->getTemplateForFile($file)) !== null) {
            $file->update(['template' => $template]);
        }

        return $template;
    }

    private function getTemplateForFile(File $file): ?string
    {
        $template = null;
        $type     = $file->type();

        if (is_null($type)) {
            return null;
        }

        if (($this->templateMap[$type] ?? null) !== null) {
            $option = $this->templateMap[$type];

            // Use file type as template name
            if ($option === true) {
                $template = $type;
            }

            // Use specified templates
            // Must check for `string` before `callable`.
            // Otherwise, some strings may be interpreted as callables unexpectedly
            if (\is_string($option)) {
                $template = $option;
            }

            // Use callable to determine template
            if (\is_callable($option)) {
                $template = $option($file);
            }
        }

        return $template !== null && $this->templateExists($template) ? $template : null;
    }

    private function templateExists(?string $template): bool
    {
        $blueprints = $this->kirby->blueprints('files');
        return in_array($template, $blueprints, true);
    }

    private function getTemplateMap(bool|array $autoAssign): array
    {
        $map = [];

        foreach (\array_keys(F::$types) as $type) {
            $map[$type] = \is_bool($autoAssign) ? $autoAssign : false;
        }

        if (\is_array($autoAssign)) {
            $map = \array_merge($map, $autoAssign);
        }

        return $map;
    }
}
