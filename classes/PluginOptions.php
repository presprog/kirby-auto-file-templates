<?php declare(strict_types=1);

namespace PresProg\AutoFileTemplates;

readonly class PluginOptions
{
    public function __construct(public bool $autoAssign) {}

    public static function createFromOptions(array $options): self
    {
        return new self(
            autoAssign: $options['autoAssign'] ?? true
        );
    }
}
