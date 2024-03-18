<?php declare(strict_types=1);

namespace PresProg\AutoFileTemplates;

readonly class PluginOptions
{
    public static function createFromOptions(array $options): self
    {
        return new self;
    }
}
