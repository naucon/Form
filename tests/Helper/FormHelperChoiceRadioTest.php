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

use Naucon\Form\FormCollectionInterface;
use Naucon\Form\FormInterface;
use Naucon\Form\Helper\FormHelperChoiceRadio;
use Naucon\Form\Mapper\EntityContainerInterface;
use Naucon\Form\Mapper\Property;
use Naucon\Form\Tests\Entities\User;
use Naucon\Utility\Map;

/**
 * Class FormHelperChoiceRadioTest
 *
 * @package Naucon\Form\Tests
 */
class FormHelperChoiceRadioTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var FormInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $form;

    /**
     * @var FormCollectionInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $formCollection;

    /**
     * @var EntityContainerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $entityContainer;

    /**
     * @var Map|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $entityContainerMap;

    protected function setUp()
    {
        parent::setUp();

        $this->form = $this->getMockBuilder(FormInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->formCollection = $this->getMockBuilder(FormCollectionInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->entityContainer = $this->getMockBuilder(EntityContainerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->entityContainerMap = $this->getMockBuilder(Map::class)
            ->disableOriginalConstructor()
            ->getMock();
    }


    public function testInit()
    {
        $userEntity = new User();
        $userEntity->setComment('foo');

        $this->entityContainerMap->expects($this->any())
            ->method('count')
            ->willReturn(1);


        $this->form->expects($this->any())
            ->method('getName')
            ->willReturn('testform');

        $this->form->expects($this->any())
            ->method('getEntityContainerMap')
            ->willReturn($this->entityContainerMap);


        $this->entityContainer->expects($this->any())
            ->method('getForm')
            ->willReturn($this->form);

        $this->entityContainer->expects($this->any())
            ->method('getEntity')
            ->willReturn($userEntity);

        $propertyObject = new Property($this->entityContainer, 'comment');

        $formHelper = new FormHelperChoiceRadio();
        $formHelper->setProperty($propertyObject);
        $formHelper->setChoice('foo');
        $formHelper->render();

        $this->assertEquals('<input type="radio" name="testform[comment]" value="foo" id="testform_comment" checked="checked" />' . PHP_EOL, $formHelper->render());
    }

    public function testRenderWithWhitelistedAttributes()
    {
        $userEntity = new User();
        $userEntity->setComment('foo');

        $this->entityContainerMap->expects($this->any())
            ->method('count')
            ->willReturn(1);


        $this->form->expects($this->any())
            ->method('getName')
            ->willReturn('testform');

        $this->form->expects($this->any())
            ->method('getEntityContainerMap')
            ->willReturn($this->entityContainerMap);


        $this->entityContainer->expects($this->any())
            ->method('getForm')
            ->willReturn($this->form);

        $this->entityContainer->expects($this->any())
            ->method('getEntity')
            ->willReturn($userEntity);

        $propertyObject = new Property($this->entityContainer, 'comment');

        $formHelper = new FormHelperChoiceRadio();
        $formHelper->setProperty($propertyObject);
        $formHelper->setChoice('foo');
        $formHelper->setOptions([
            'id' => 'foo2',
            'class' => 'form-control',
            'data-attr' => 'data-attribute'
        ]);
        $this->assertEquals('<input type="radio" name="testform[comment]" value="foo" id="foo2" checked="checked" class="form-control" data-attr="data-attribute" />' . PHP_EOL, $formHelper->render());
    }

    public function testHelperWithOneFormCollection()
    {
        $userEntity = new User();
        $userEntity->setComment('foo');

        $this->entityContainerMap->expects($this->any())
            ->method('count')
            ->willReturn(1);


        $this->formCollection->expects($this->any())
            ->method('getName')
            ->willReturn('testform');

        $this->formCollection->expects($this->any())
            ->method('getEntityContainerMap')
            ->willReturn($this->entityContainerMap);


        $this->entityContainer->expects($this->any())
            ->method('getForm')
            ->willReturn($this->formCollection);

        $this->entityContainer->expects($this->any())
            ->method('getName')
            ->willReturn(0);

        $this->entityContainer->expects($this->any())
            ->method('getEntity')
            ->willReturn($userEntity);

        $propertyObject = new Property($this->entityContainer, 'comment');

        $formHelper = new FormHelperChoiceRadio();
        $formHelper->setProperty($propertyObject);
        $formHelper->setChoice('foo');
        $formHelper->render();

        $this->assertEquals('<input type="radio" name="testform[0][comment]" value="foo" id="testform_0_comment" checked="checked" />' . PHP_EOL, $formHelper->render());
    }
}
