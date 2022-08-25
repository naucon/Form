<?php

namespace Naucon\Form\Tests\Helper\Twig;

use Naucon\Form\Configuration;
use Naucon\Form\FormInterface;
use Naucon\Form\Helper\AbstractFormHelperField;
use Naucon\Form\Helper\Twig\NcFormExtension;
use Naucon\Form\Mapper\EntityContainer;
use Naucon\Form\Mapper\Property;
use Naucon\Utility\Iterator;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Twig\TwigFunction;

class NcFormExtensionTest extends TestCase
{

    /**
     * @var FormInterface&MockObject
     */
    private $form;
    /**
     * @var Configuration&MockObject
     */
    private $configuration;
    /**
     * @var Iterator&MockObject
     */
    private $iterator;
    /**
     * @var AbstractFormHelperField&MockObject
     */
    private $formHelperField;
    /**
     * @var Property&MockObject
     */
    private $property;
    /**
     * @var EntityContainer&MockObject
     */
    private $entityContainer;

    public function testFormEnd()
    {
        $extension = $this->createExtension();
        $this->configureMocks();

        $html = $extension->formEnd($this->form);
        $this->assertStringContainsString('</form>', $html);

    }

    public function testField()
    {
        $extension = $this->createExtension();
        $this->configureMocks();
        $this->iterator
            ->expects($this->once())
            ->method('current')
            ->willReturn($this->formHelperField);

        $this->formHelperField
            ->expects($this->once())
            ->method('getProperty')
            ->willReturn($this->property);

        $this->property
            ->expects($this->once())
            ->method('getEntityContainer')
            ->willReturn($this->entityContainer);

        $this->entityContainer
            ->expects($this->once())
            ->method('hasError')
            ->willReturn(false);

        $html = $extension->field($this->form, 'text', 'my-name', ['class' => 'css-class', 'data-foo' => 'some-attribute']);
        $this->assertStringContainsString('<input type="text"', $html);
        $this->assertStringContainsString('class="css-class"', $html);
        $this->assertStringContainsString('data-foo="some-attribute"', $html);

    }

    public function testGetFunctions()
    {
        $extension = $this->createExtension();
        $functions = $extension->getFunctions();

        $this->assertCount(3, $functions);
        $this->assertContainsOnlyInstancesOf(TwigFunction::class, $functions);


    }

    public function testFormStart()
    {
        $extension = $this->createExtension();
        $this->configureMocks();

        $html = $extension->formStart($this->form);
        $this->assertStringContainsString('<form', $html);
        $this->assertStringContainsString('method="post"', $html);

    }

    protected function createExtension(): NcFormExtension
    {
        return new NcFormExtension();
    }

    protected function configureMocks()
    {
        $this->form
            ->expects($this->once())
            ->method('getConfiguration')
            ->willReturn($this->configuration);

        $this->form
            ->expects($this->once())
            ->method('getEntityContainerIterator')
            ->willReturn($this->iterator);

        $this->configuration
            ->expects($this->once())
            ->method('get')
            ->with('csrf_protection')
            ->willReturn(false);
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->form = $this->createMock(FormInterface::class);
        $this->configuration = $this->createMock(Configuration::class);
        $this->iterator = $this->createMock(Iterator::class);
        $this->formHelperField = $this->createMock(AbstractFormHelperField::class);
        $this->property = $this->createMock(Property::class);
        $this->entityContainer = $this->createMock(EntityContainer::class);
    }
}
