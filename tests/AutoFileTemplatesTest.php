<?php declare(strict_types=1);

namespace PresProg\AutoFileTemplates\Tests;

use Kirby\Cms\App;
use Kirby\Cms\File;
use Kirby\Cms\Page;
use Kirby\Filesystem\Dir;
use Kirby\Filesystem\F;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use PresProg\AutoFileTemplates\AutoFileTemplates;
use PresProg\AutoFileTemplates\PluginOptions;

class AutoFileTemplatesTest extends TestCase
{
    private static string $tmpDir;


    public static function setUpBeforeClass(): void
    {
        self::$tmpDir = __DIR__ . '/temp';

    }

    protected function setUp(): void
    {
        // Do not register any error handler
        App::$enableWhoops = false;

        Dir::copy(__DIR__ . '/fixtures', self::$tmpDir);
    }

    protected function tearDown(): void
    {
        Dir::remove(self::$tmpDir);
    }

    #[DataProvider('files')]
    public function testSetsTemplateForCoreFileTypes(File $file, ?string $expected): void
    {
        $kirby = new App([
            'roots' => [
                'index' => self::$tmpDir,
            ],
            'options' => [
                'presprog.auto-file-templates' => [
                    'autoAssign' => true,
                ],
            ],
        ]);

        $service  = new AutoFileTemplates($kirby, PluginOptions::createFromOptions($kirby->options()));
        $template = $service->autoAssign($file);
        $this->assertEquals($expected, $template);
    }

    public function testSetsTemplateForCustomFileTypes(): void
    {
        $kirby = new App([
            'roots' => [
                'index' => self::$tmpDir,
            ],
            'options' => [
                'presprog.auto-file-templates' => [
                    'autoAssign' => true,
                ],
            ],
        ]);

        F::$types['custom-type'] = ['custom'];

        $props = ['type' => 'custom-type', 'filename' => 'image.custom', 'parent' => self::page()];

        $file = $this->getMockBuilder(File::class)
                     ->disableOriginalConstructor()
                     ->setConstructorArgs($props)
                     ->getMock()
        ;

        $file->expects($this->once())
             ->method('update')
             ->with(['template' => 'custom-type'])
        ;

        $file->method('type')
             ->willReturn($props['type'])
        ;

        $service  = new AutoFileTemplates($kirby, PluginOptions::createFromOptions($kirby->options()));
        $template = $service->autoAssign($file);
        $this->assertEquals('custom-type', $template);
    }

    public function testFileIsUpdated(): void
    {
        $kirby = new App([
            'roots' => [
                'index' => self::$tmpDir,
            ],
            'options' => [
                'presprog.auto-file-templates' => [
                    'autoAssign' => true,
                ],
            ],
        ]);

        $service  = new AutoFileTemplates($kirby, PluginOptions::createFromOptions($kirby->options()));

        // File One: An image file without a template
        $fileOneProps = ['type' => 'image', 'filename' => 'image.png', 'parent' => self::page()];

        $fileOne = $this->getMockBuilder(File::class)
                     ->disableOriginalConstructor()
                     ->setConstructorArgs($fileOneProps)
                     ->getMock()
        ;

        $fileOne->expects($this->once())
             ->method('update')
             ->with(['template' => 'image'])
        ;

        $fileOne->method('type')
             ->willReturn($fileOneProps['type'])
        ;


        $templateOne = $service->autoAssign($fileOne);
        $this->assertEquals('image', $templateOne);

        // File Two  An image file with template `photo` (will not be updated)

        $fileTwoProps = ['type' => 'image', 'filename' => 'image.png', 'parent' => self::page(), 'template' => 'photo'];

        $fileTwo = $this->getMockBuilder(File::class)
                        ->disableOriginalConstructor()
                        ->setConstructorArgs($fileTwoProps)
                        ->getMock()
        ;

        $fileTwo->method('template')
                ->willReturn($fileTwoProps['template'])
        ;

        $fileTwo->expects($this->never())
                ->method('update')
        ;

        $fileTwo->method('type')
                ->willReturn($fileTwoProps['type'])
        ;

        $templateTwo = $service->autoAssign($fileTwo);
        $this->assertEquals(null, $templateTwo);
    }

    public function testDoesNothingIfTurnedOffByOption(): void
    {
        $options = [
            'autoAssign' => false,
        ];

        $kirby = new App([
            'roots' => [
                'index' => self::$tmpDir,
            ],
            'options' => [
                'presprog.auto-file-templates' => $options,
            ],
        ]);

        $file = $this->file();

        $service  = new AutoFileTemplates($kirby, PluginOptions::createFromOptions($kirby->options()));
        $template = $service->autoAssign($file);
        $this->assertEquals(null, $template);
    }

    public function testSetTemplateFromString(): void
    {
        $options = [
            'autoAssign' => [
                'image' => 'vector',
            ],
        ];

        $kirby = new App([
            'roots' => [
                'index' => self::$tmpDir,
            ],
            'options' => [
                'presprog.auto-file-templates' => $options,
            ],
        ]);

        $service = new AutoFileTemplates($kirby, PluginOptions::createFromOptions($kirby->options()));

        // File is of type image, but we expect `vector` to be applied here
        $image = new File(['type' => 'image', 'filename' => 'image.svg', 'parent' => self::page()]);

        $this->assertEquals('vector', $service->autoAssign($image));
    }


    public function testTemplatesAsCallable(): void
    {
        $options = [
            'autoAssign' => [
                'image' => function (File $file) {
                    return match (F::extension($file->filename())) {
                        'svg'   => 'vector',
                        default => 'image',
                    };
                },
            ],
        ];

        $kirby = new App([
            'roots' => [
                'index' => self::$tmpDir,
            ],
            'options' => [
                'presprog.auto-file-templates' => $options,
            ],
        ]);

        $service = new AutoFileTemplates($kirby, PluginOptions::createFromOptions($kirby->options()));

        // Both files are of type `image` but will get have different tempaltes assigned based on their extension
        $image  = new File(['type' => 'image', 'filename' => 'image.png', 'parent' => self::page()]);
        $vector = new File(['type' => 'image', 'filename' => 'image.svg', 'parent' => self::page()]);

        $this->assertEquals('image', $service->autoAssign($image));
        $this->assertEquals('vector', $service->autoAssign($vector));
    }

    public function testOnlyTurnOnAutoAssignForSomeFileTypes(): void
    {
        $options = [
            'autoAssign' => [
                'image' => true,
            ],
        ];

        $kirby = new App([
            'roots' => [
                'index' => self::$tmpDir,
            ],
            'options' => [
                'presprog.auto-file-templates' => $options,
            ],
        ]);

        $service = new AutoFileTemplates($kirby, PluginOptions::createFromOptions($kirby->options()));

        // Auto-assigning a template for `image` is disabled, but `document` should just work fine
        $image    = new File(['type' => 'image', 'filename' => 'image.png', 'parent' => self::page()]);
        $document = new File(['type' => 'document', 'filename' => 'document.pdf', 'parent' => self::page()]);

        $this->assertEquals('image', $service->autoAssign($image));
        $this->assertEquals(null, $service->autoAssign($document));
    }

    public function testDoNotOverwriteExistingTemplateByDefault(): void
    {
        $options = [
            'autoAssign' => true,
        ];

        $kirby = new App([
            'roots' => [
                'index' => self::$tmpDir,
            ],
            'options' => [
                'presprog.auto-file-templates' => $options,
            ],
        ]);

        $service = new AutoFileTemplates($kirby, PluginOptions::createFromOptions($kirby->options()));

        $image    = new File(['type' => 'image', 'filename' => 'image.png', 'parent' => self::page(), 'template' => 'photo']);

        $this->assertEquals(null, $service->autoAssign($image));
        $this->assertEquals('photo', $image->template());
    }


    public function testOverwriteTemplateIfOptionIsSet(): void
    {
        $options = [
            'autoAssign' => true,
            'forceOverwrite' => true,
        ];

        $kirby = new App([
            'roots' => [
                'index' => self::$tmpDir,
            ],
            'options' => [
                'presprog.auto-file-templates' => $options,
            ],
        ]);

        $service = new AutoFileTemplates($kirby, PluginOptions::createFromOptions($kirby->options()));

        $image    = new File(['type' => 'image', 'filename' => 'image.png', 'parent' => self::page(), 'template' => 'photo']);

        $this->assertEquals('image', $service->autoAssign($image));
    }

    public function testFileTypeCannotBeDetermined(): void
    {
        $options = [
            'autoAssign' => true,
        ];

        $kirby = new App([
            'roots' => [
                'index' => self::$tmpDir,
            ],
            'options' => [
                'presprog.auto-file-templates' => $options,
            ],
        ]);

        $service = new AutoFileTemplates($kirby, PluginOptions::createFromOptions($kirby->options()));
        $image   = new File(['type' => 'sometype', 'filename' => 'file', 'parent' => self::page()]);

        $this->assertEquals(null, $service->autoAssign($image));
    }

    public static function files(): \Generator
    {
        $page = self::page();

        // No template will be set
        yield 'archive' => [new File(['type' => 'archive', 'filename' => 'archive.zip', 'parent' => $page]), null];

        // Templates will be set
        yield 'audio' => [new File(['type' => 'audio', 'filename' => 'audio.mp3', 'parent' => $page]), 'audio'];
        yield 'code' => [new File(['type' => 'code', 'filename' => 'code.php', 'parent' => $page]), 'code'];
        yield 'document' => [new File(['type' => 'document', 'filename' => 'document.pdf', 'parent' => $page]), 'document'];
        yield 'image' => [new File(['type' => 'image', 'filename' => 'image.png', 'parent' => $page]), 'image'];
        yield 'video' => [new File(['type' => 'video', 'filename' => 'video.mp4', 'parent' => $page]), 'video'];
    }

    public function file(): File
    {
        return new File(['type' => 'image', 'filename' => 'image.png', 'parent' => self::page()]);
    }

    public static function page(): Page
    {
        return new Page(['title' => 'Test', 'slug' => 'test']);
    }
}
