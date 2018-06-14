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

use Naucon\Form\FormInterface;
use Naucon\Form\Helper\FormHelperFieldName;
use Naucon\Form\Mapper\EntityContainerInterface;
use Naucon\Form\Mapper\Property;
use Naucon\Form\Tests\Entities\User;

/**
 * Class FormHelperFieldNameTest
 *
 * @package Naucon\Form\Tests
 */
class FormHelperFieldNameTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var FormInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $form;

    /**
     * @var EntityContainerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $entityContainer;


    protected function setUp()
    {
        parent::setUp();

        $this->form = $this->getMockBuilder(FormInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->entityContainer = $this->getMockBuilder(EntityContainerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
    }


    public function testInit()
    {
        $userEntity = new User();
        $userEntity->setComment('foo');

        $entityContainerMap = $this->getMock('Naucon\Utility\Map');
        $entityContainerMap->expects($this->any())
            ->method('count')
            ->will($this->returnValue(1));

        $this->form->expects($this->any())
            ->method('getName')
            ->will($this->returnValue('testform'));
        $this->form->expects($this->any())
            ->method('getEntityContainerMap')
            ->will($this->returnValue($entityContainerMap));


        $this->entityContainer->expects($this->any())
            ->method('getForm')
            ->will($this->returnValue($this->form));

        $this->entityContainer->expects($this->any())
            ->method('getEntity')
            ->will($this->returnValue($userEntity));

        $propertyObject = new Property($this->entityContainer, 'comment');

        $formHelper = new FormHelperFieldName();
        $formHelper->setProperty($propertyObject);
        $formHelper->render();

        $this->assertEquals('testform[comment]', $formHelper->render());
    }
}