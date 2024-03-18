<?php declare(strict_types=1);

namespace PresProg\AutoFileTemplates;

use Kirby\Cms\App;
use Kirby\Cms\File;

class AutoFileTemplates
{
    public function __construct(
        private App $kirby,
        private PluginOptions $options,
    ) {}

    public function setTemplate(File $file): ?string
    {
        $this->kirby->impersonate('kirby');

        $template = match ($file->type()) {
            'audio' => 'audio',
            'archive' => 'archive',
            'code' => 'code',
            'document' => 'document',
            'image' => 'image',
            'video' => 'video',
            default => null,
        };

        if (!$template) {
            return null;
        }

        $file->update(['template' => $template]);
        $file->save();

        return $template;
    }
}
