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
use Naucon\Form\Helper\FormHelperFieldTextarea;
use Naucon\Form\Mapper\EntityContainerInterface;
use Naucon\Form\Mapper\Property;
use Naucon\Form\Tests\Entities\User;
use Naucon\Utility\Map;

/**
 * Class FormHelperFieldTextareaTest
 *
 * @package Naucon\Form\Tests
 */
class FormHelperFieldTextareaTest extends \PHPUnit_Framework_TestCase
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
            ->will($this->returnValue(1));


        $this->form->expects($this->any())
            ->method('getName')
            ->will($this->returnValue('testform'));

        $this->form->expects($this->any())
            ->method('getEntityContainerMap')
            ->will($this->returnValue($this->entityContainerMap));


        $this->entityContainer->expects($this->any())
            ->method('getForm')
            ->will($this->returnValue($this->form));

        $this->entityContainer->expects($this->any())
            ->method('getEntity')
            ->will($this->returnValue($userEntity));

        $propertyObject = new Property($this->entityContainer, 'comment');

        $formHelper = new FormHelperFieldTextarea();
        $formHelper->setProperty($propertyObject);
        $formHelper->render();

        $this->assertEquals('<textarea name="testform[comment]" id="testform_comment">foo</textarea>' . PHP_EOL, $formHelper->render());
    }

    public function testHelperWithOneFormCollection()
    {
        $userEntity = new User();
        $userEntity->setComment('foo');

        $this->entityContainerMap->expects($this->any())
            ->method('count')
            ->will($this->returnValue(1));


        $this->formCollection->expects($this->any())
            ->method('getName')
            ->will($this->returnValue('testform'));

        $this->formCollection->expects($this->any())
            ->method('getEntityContainerMap')
            ->will($this->returnValue($this->entityContainerMap));


        $this->entityContainer->expects($this->any())
            ->method('getForm')
            ->will($this->returnValue($this->formCollection));

        $this->entityContainer->expects($this->any())
            ->method('getName')
            ->will($this->returnValue(0));

        $this->entityContainer->expects($this->any())
            ->method('getEntity')
            ->will($this->returnValue($userEntity));

        $propertyObject = new Property($this->entityContainer, 'comment');

        $formHelper = new FormHelperFieldTextarea();
        $formHelper->setProperty($propertyObject);
        $formHelper->render();

        $this->assertEquals('<textarea name="testform[0][comment]" id="testform_0_comment">foo</textarea>' . PHP_EOL, $formHelper->render());
    }

    public function testRenderWithValueAttribute()
    {
        $userEntity = new User();

        $this->entityContainerMap->expects($this->any())
            ->method('count')
            ->will($this->returnValue(1));


        $this->form->expects($this->any())
            ->method('getName')
            ->will($this->returnValue('testform'));

        $this->form->expects($this->any())
            ->method('getEntityContainerMap')
            ->will($this->returnValue($this->entityContainerMap));


        $this->entityContainer->expects($this->any())
            ->method('getForm')
            ->will($this->returnValue($this->form));

        $this->entityContainer->expects($this->any())
            ->method('getEntity')
            ->will($this->returnValue($userEntity));

        $propertyObject = new Property($this->entityContainer, 'comment');

        $formHelper = new FormHelperFieldTextarea();
        $formHelper->setProperty($propertyObject);
        $formHelper->setOptions([
           'value' => 'bar'
        ]);
        $formHelper->render();

        $this->assertEquals('<textarea name="testform[comment]" id="testform_comment">bar</textarea>' . PHP_EOL, $formHelper->render());
    }

    public function testRenderWithWhitelistedAttributes()
    {
        $userEntity = new User();
        $userEntity->setComment('foo');

        $this->entityContainerMap->expects($this->any())
            ->method('count')
            ->will($this->returnValue(1));


        $this->form->expects($this->any())
            ->method('getName')
            ->will($this->returnValue('testform'));

        $this->form->expects($this->any())
            ->method('getEntityContainerMap')
            ->will($this->returnValue($this->entityContainerMap));


        $this->entityContainer->expects($this->any())
            ->method('getForm')
            ->will($this->returnValue($this->form));

        $this->entityContainer->expects($this->any())
            ->method('getEntity')
            ->will($this->returnValue($userEntity));

        $propertyObject = new Property($this->entityContainer, 'comment');

        $formHelper = new FormHelperFieldTextarea();
        $formHelper->setProperty($propertyObject);
        $formHelper->setOptions([
            'id' => 'foo2',
            'class' => 'form-control'
        ]);
        $formHelper->render();

        $this->assertEquals('<textarea name="testform[comment]" id="foo2" class="form-control">foo</textarea>' . PHP_EOL, $formHelper->render());
    }
}