<?php
/*
 * Copyright 2008 Sven Sanzenbacher
 *
 * This file is part of the naucon package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Naucon\Form\Tests\Helper;

use Naucon\Form\FormCollectionInterface;
use Naucon\Form\Helper\FormHelperOptionRadio;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Class FormHelperOptionRadioTest
 *
 * @package Naucon\Form\Tests
 */
class FormHelperOptionRadioTest extends TestCase
{
    /**
     * @var FormCollectionInterface|MockObject
     */
    protected $formCollection;

    protected function setUp(): void
    {
        parent::setUp();

        $this->formCollection = $this->getMockBuilder(FormCollectionInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
    }


    public function testInit()
    {
        $this->formCollection->expects($this->any())
            ->method('getFormOptionName')
            ->willReturn('ncform_option');

        $formHelper = new FormHelperOptionRadio();
        $formHelper->setForm($this->formCollection);
        $formHelper->setChoice('foo');
        $formHelper->render();

        $this->assertEquals('<input type="radio" name="ncform_option" value="foo" />' . PHP_EOL, $formHelper->render());
    }

    public function testHelperWithDefinedOption()
    {
        $this->formCollection->expects($this->any())
            ->method('getFormOptionName')
            ->willReturn('ncform_option');

        $this->formCollection->expects($this->any())
            ->method('isFormOptionSelected')
            ->willReturn('foo');

        $formHelper = new FormHelperOptionRadio();
        $formHelper->setForm($this->formCollection);
        $formHelper->setChoice('foo');
        $formHelper->render();

        $this->assertEquals('<input type="radio" name="ncform_option" value="foo" checked="checked" />' . PHP_EOL, $formHelper->render());
    }
}
