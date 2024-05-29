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

use Naucon\Form\Helper\FormHelperChoiceSelect;
use Naucon\Form\Mapper\Property;
use Naucon\Form\Tests\Entities\User;
use Naucon\Utility\Map;
use Naucon\Form\FormInterface;
use Naucon\Form\Mapper\EntityContainerInterface;
use Naucon\Form\FormCollectionInterface;
use PHPUnit\Framework\TestCase;

class FormHelperChoiceSelectTest extends TestCase
{
    public function testInit()
    {
        $userEntity = new User();
        $userEntity->setComment('foo');

        $entityContainerMap = $this->getMockBuilder(Map::class)->getMock();
        $entityContainerMap
            ->expects($this->any())
            ->method('count')
            ->willReturn(1);

        $form = $this->getMockBuilder(FormInterface::class)->getMock();
        $form
            ->expects($this->any())
            ->method('getName')
            ->willReturn('testform');
        $form
            ->expects($this->any())
            ->method('getEntityContainerMap')
            ->willReturn($entityContainerMap);

        $entityContainer = $this->getMockBuilder(EntityContainerInterface::class)->getMock();
        $entityContainer
            ->expects($this->any())
            ->method('getForm')
            ->willReturn($form);

        $entityContainer
            ->expects($this->any())
            ->method('getEntity')
            ->willReturn($userEntity);

        $propertyObject = new Property($entityContainer, 'comment');

        $formHelper = new FormHelperChoiceSelect();
        $formHelper->setProperty($propertyObject);
        $formHelper->setChoice('foo');
        $formHelper->render();

        $expectedString = '<select name="testform[comment]" id="testform_comment"><option selected="selected">foo</option></select>' . PHP_EOL;
        $this->assertEquals($expectedString, $formHelper->render());

        $formHelper = new FormHelperChoiceSelect();
        $formHelper->setProperty($propertyObject);
        $formHelper->setChoices(['bar' => 'Bar', 'foo' => 'Foo']);
        $formHelper->render();

        $expectedString = '<select name="testform[comment]" id="testform_comment">';
        $expectedString .= '<option value="bar">Bar</option>';
        $expectedString .= '<option value="foo" selected="selected">Foo</option>';
        $expectedString .= '</select>' . PHP_EOL;
        $this->assertEquals($expectedString, $formHelper->render());
    }

    public function testHelperWithOneFormCollection()
    {
        $userEntity = new User();
        $userEntity->setComment('foo');

        $entityContainerMap = $this->getMockBuilder(Map::class)->getMock();
        $entityContainerMap
            ->expects($this->any())
            ->method('count')
            ->willReturn(1);

        $form = $this->createMock(FormCollectionInterface::class);
        $form
            ->expects($this->any())
            ->method('getName')
            ->willReturn('testform');
        $form
            ->expects($this->any())
            ->method('getEntityContainerMap')
            ->willReturn($entityContainerMap);

        $entityContainer = $this->getMockBuilder(EntityContainerInterface::class)->getMock();
        $entityContainer
            ->expects($this->any())
            ->method('getForm')
            ->willReturn($form);

        $entityContainer
            ->expects($this->any())
            ->method('getName')
            ->willReturn(0);

        $entityContainer
            ->expects($this->any())
            ->method('getEntity')
            ->willReturn($userEntity);

        $propertyObject = new Property($entityContainer, 'comment');

        $formHelper = new FormHelperChoiceSelect();
        $formHelper->setProperty($propertyObject);
        $formHelper->setChoice('foo');
        $formHelper->render();

        $expectedString = '<select name="testform[0][comment]" id="testform_0_comment"><option selected="selected">foo</option></select>' . PHP_EOL;
        $this->assertEquals($expectedString, $formHelper->render());

        $formHelper = new FormHelperChoiceSelect();
        $formHelper->setProperty($propertyObject);
        $formHelper->setChoices(['bar' => 'Bar', 'foo' => 'Foo']);
        $formHelper->render();

        $expectedString = '<select name="testform[0][comment]" id="testform_0_comment">';
        $expectedString .= '<option value="bar">Bar</option>';
        $expectedString .= '<option value="foo" selected="selected">Foo</option>';
        $expectedString .= '</select>' . PHP_EOL;
        $this->assertEquals($expectedString, $formHelper->render());
    }
}
