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
use Naucon\Form\Helper\FormHelperFieldText;
use Naucon\Form\Mapper\EntityContainerInterface;
use Naucon\Form\Mapper\Property;
use Naucon\Form\Tests\Entities\User;
use Naucon\Utility\Map;

/**
 * Class FormHelperFieldTextTest
 *
 * @package Naucon\Form\Tests
 */
class FormHelperFieldTextTest extends \PHPUnit_Framework_TestCase
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

        $formHelper = new FormHelperFieldText();
        $formHelper->setProperty($propertyObject);
        $formHelper->render();

        $this->assertEquals('<input type="text" name="testform[comment]" value="foo" id="testform_comment" />' . PHP_EOL, $formHelper->render());
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

        $formHelper = new FormHelperFieldText();
        $formHelper->setProperty($propertyObject);
        $formHelper->render();

        $this->assertEquals('<input type="text" name="testform[0][comment]" value="foo" id="testform_0_comment" />' . PHP_EOL, $formHelper->render());
    }

    public function testHelperWithSpecialChar()
    {
        $userEntity = new User();
        $userEntity->setComment('">&Römer');

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

        $formHelper = new FormHelperFieldText();
        $formHelper->setProperty($propertyObject);
        $formHelper->render();

        $this->assertEquals('<input type="text" name="testform[comment]" value="&#34;>&#38;Römer" id="testform_comment" />' . PHP_EOL, $formHelper->render());
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

        $formHelper = new FormHelperFieldText();
        $formHelper->setProperty($propertyObject);
        $formHelper->setOptions([
            'id' => 'foo2',
            'class' => 'form-control'
        ]);
        $formHelper->render();

        $this->assertEquals('<input type="text" name="testform[comment]" value="foo" id="foo2" class="form-control" />' . PHP_EOL, $formHelper->render());
    }
}
