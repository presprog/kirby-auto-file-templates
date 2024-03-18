<?php declare(strict_types=1);

namespace PresProg\AutoFileTemplates\Tests;

use Kirby\Cms\App;
use Kirby\Cms\File;
use Kirby\Cms\Page;
use Kirby\Filesystem\Dir;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use PresProg\AutoFileTemplates\AutoFileTemplates;
use PresProg\AutoFileTemplates\PluginOptions;

class AutoFileTemplatesTest extends TestCase
{
    private static string $tmpDir;


    public static function setUpBeforeClass(): void
    {
        self::$tmpDir   = __DIR__ . '/temp';

    }

    protected function setUp(): void
    {
        // Do not register any error handler
        App::$enableWhoops = false;

        Dir::copy(__DIR__. '/fixtures', self::$tmpDir);
    }

    protected function tearDown(): void
    {
        Dir::remove(self::$tmpDir);
    }

    #[DataProvider('files')]
    public function testSetsTemplate(File $file, string $expected): void
    {
        $kirby = new App([
            'roots' => [
                'index' => self::$tmpDir,
            ],
        ]);

        $service  = new AutoFileTemplates($kirby, PluginOptions::createFromOptions([]));
        $template = $service->setTemplate($file);
        $this->assertEquals($expected, $template);
    }

    public static function files(): \Generator
    {
        $page = new Page(['title' => 'Test', 'slug' => 'test']);
        yield 'audio' => [new File(['type' => 'audio', 'filename' => 'audio.mp3', 'parent' => $page]), 'audio'];
        yield 'code' => [new File(['type' => 'code', 'filename' => 'code.php', 'parent' => $page]), 'code'];
        yield 'document' => [new File(['type' => 'document', 'filename' => 'document.pdf', 'parent' => $page]), 'document'];
        yield 'image' => [new File(['type' => 'image', 'filename' => 'image.jpg', 'parent' => $page]), 'image'];
        yield 'video' => [new File(['type' => 'video', 'filename' => 'video.mp4', 'parent' => $page]), 'video'];
    }
}
