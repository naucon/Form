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

use Naucon\Form\Helper\FormHelperFieldValue;
use Naucon\Form\Mapper\Property;
use Naucon\Form\Tests\Entities\User;

class FormHelperFieldValueTest extends \PHPUnit_Framework_TestCase
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

        $formHelper = new FormHelperFieldValue();
        $formHelper->setProperty($propertyObject);
        $formHelper->render();

        $this->assertEquals('foo', $formHelper->render());
    }
}