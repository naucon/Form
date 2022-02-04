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
use Naucon\Utility\Map;
use Naucon\Form\FormInterface;
use Naucon\Form\Mapper\EntityContainerInterface;
use PHPUnit\Framework\TestCase;

class FormHelperFieldValueTest extends TestCase
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

        $formHelper = new FormHelperFieldValue();
        $formHelper->setProperty($propertyObject);
        $formHelper->render();

        $this->assertEquals('foo', $formHelper->render());
    }
}
