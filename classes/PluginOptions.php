<?php declare(strict_types=1);

namespace PresProg\AutoFileTemplates;

readonly class PluginOptions
{
    public function __construct(public array|bool $autoAssign, public bool $forceOverwrite)
    {
    }

    public static function createFromOptions(array $options): self
    {
        $pluginOptions = $options['presprog.auto-file-templates'] ?? [];

        return new self(
            autoAssign: $pluginOptions['autoAssign'] ?? true,
            forceOverwrite: $pluginOptions['forceOverwrite'] ?? false,
        );
    }
}
