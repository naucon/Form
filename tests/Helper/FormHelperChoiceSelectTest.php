<?php
/*
 * Copyright 2008 Sven Sanzenbacher
 *
 * This file is part of the naucon package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Naucon\Form\Tests;

use Naucon\Form\Helper\FormHelperChoiceSelect;
use Naucon\Form\Mapper\Property;
use Naucon\Form\Tests\Entities\User;

class FormHelperChoiceSelectTest extends \PHPUnit_Framework_TestCase
{
    public function testInit()
    {
        $userEntity = new User();
        $userEntity->setComment('foo');

        $entityContainerMap = $this->getMock('Naucon\Utility\Map');
        $entityContainerMap->expects($this->any())
            ->method('count')
            ->will($this->returnValue(1));

        $form = $this->getMock('Naucon\Form\FormInterface');
        $form->expects($this->any())
            ->method('getName')
            ->will($this->returnValue('testform'));
        $form->expects($this->any())
            ->method('getEntityContainerMap')
            ->will($this->returnValue($entityContainerMap));

        $entityContainer = $this->getMock('Naucon\Form\Mapper\EntityContainerInterface');
        $entityContainer->expects($this->any())
            ->method('getForm')
            ->will($this->returnValue($form));

        $entityContainer->expects($this->any())
            ->method('getEntity')
            ->will($this->returnValue($userEntity));

        $propertyObject = new Property($entityContainer, 'comment');

        $formHelper = new FormHelperChoiceSelect();
        $formHelper->setProperty($propertyObject);
        $formHelper->setChoice('foo');
        $formHelper->render();

        $expectedString = '<select name="testform[comment]" id="testform_comment"><option selected="selected">foo</option></select>' . PHP_EOL;
        $this->assertEquals($expectedString, $formHelper->render());

        $formHelper = new FormHelperChoiceSelect();
        $formHelper->setProperty($propertyObject);
        $formHelper->setChoices(array('bar' => 'Bar', 'foo' => 'Foo'));
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

        $entityContainerMap = $this->getMock('Naucon\Utility\Map');
        $entityContainerMap->expects($this->any())
            ->method('count')
            ->will($this->returnValue(1));

        $form = $this->getMock('Naucon\Form\FormCollectionInterface');
        $form->expects($this->any())
            ->method('getName')
            ->will($this->returnValue('testform'));
        $form->expects($this->any())
            ->method('getEntityContainerMap')
            ->will($this->returnValue($entityContainerMap));

        $entityContainer = $this->getMock('Naucon\Form\Mapper\EntityContainerInterface');
        $entityContainer->expects($this->any())
            ->method('getForm')
            ->will($this->returnValue($form));

        $entityContainer->expects($this->any())
            ->method('getName')
            ->will($this->returnValue(0));

        $entityContainer->expects($this->any())
            ->method('getEntity')
            ->will($this->returnValue($userEntity));

        $propertyObject = new Property($entityContainer, 'comment');

        $formHelper = new FormHelperChoiceSelect();
        $formHelper->setProperty($propertyObject);
        $formHelper->setChoice('foo');
        $formHelper->render();

        $expectedString = '<select name="testform[0][comment]" id="testform_0_comment"><option selected="selected">foo</option></select>' . PHP_EOL;
        $this->assertEquals($expectedString, $formHelper->render());

        $formHelper = new FormHelperChoiceSelect();
        $formHelper->setProperty($propertyObject);
        $formHelper->setChoices(array('bar' => 'Bar', 'foo' => 'Foo'));
        $formHelper->render();

        $expectedString = '<select name="testform[0][comment]" id="testform_0_comment">';
        $expectedString .= '<option value="bar">Bar</option>';
        $expectedString .= '<option value="foo" selected="selected">Foo</option>';
        $expectedString .= '</select>' . PHP_EOL;
        $this->assertEquals($expectedString, $formHelper->render());
    }
}