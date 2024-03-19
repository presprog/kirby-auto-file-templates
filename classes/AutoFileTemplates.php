<?php declare(strict_types=1);

namespace PresProg\AutoFileTemplates;

use Kirby\Cms\App;
use Kirby\Cms\File;
use Kirby\Filesystem\F;

readonly class AutoFileTemplates
{
    public function __construct(
        private App $kirby,
        private PluginOptions $options,
    ) {
    }

    public function autoAssign(File $file): ?string
    {
        if (!$this->shouldAutoAssign($file)) {
            return null;
        }

        // Do not overwrite existing templates
        if ($file->template() !== null) {
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
        if (is_array($this->options->autoAssign) && \array_key_exists($file->type(), $this->options->autoAssign)) {
            $template = $this->options->autoAssign[$file->type()] ?? null;

            if (\is_bool($template) && $template === true) {
                return $file->type();
            }

            // First check for `string` and `null`, then `callable`.
            // Otherwise, some strings may be interpreted as callables unexpectedly
            if (\is_string($template) || \is_null($template)) {
                return $template;
            }

            if (\is_callable($template)) {
                return $template($file);
            }

            return null;
        }

        $template = $file->type();

        if (!$this->fileTypeExists($template)) {
            return null;
        }

        if (!$this->templateExists($template)) {
            return null;
        }

        return $template;
    }

    private function fileTypeExists(?string $type): bool
    {
        return array_key_exists($type, F::$types);
    }

    private function templateExists(?string $template): bool
    {
        $blueprints = $this->kirby->blueprints('files');
        return in_array($template, $blueprints, true);
    }

    private function shouldAutoAssign(File $file): bool
    {
        if (\is_bool($this->options->autoAssign)) {
            return $this->options->autoAssign;
        }

        if (\is_array($this->options->autoAssign)) {
            $template = $this->options->autoAssign[$file->type()] ?? null;

            // Support template name
            if (is_string($template)) {
                return true;
            }

            // Support on/off toggle
            if (is_bool($template)) {
                return $template;
            }

            // Support callable that determines the template
            if (\is_callable($template ?? null)) {
                return true;
            }

            return false;
        }

        return true;
    }

}
