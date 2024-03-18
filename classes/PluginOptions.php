<?php declare(strict_types=1);

namespace PresProg\AutoFileTemplates;

readonly class PluginOptions
{
    public function __construct(public bool $autoAssign, public array $templates) {}

    public static function createFromOptions(array $options): self
    {
        $pluginOptions = $options['presprog.auto-file-templates'];

        return new self(
            autoAssign: $pluginOptions['autoAssign'] ?? true,
            templates: $pluginOptions['templates'] ?? [],
        );
    }
}

