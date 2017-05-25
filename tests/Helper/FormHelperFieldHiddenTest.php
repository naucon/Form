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

use Naucon\Form\Helper\FormHelperFieldHidden;
use Naucon\Form\Mapper\Property;
use Naucon\Form\Tests\Entities\User;

class FormHelperFieldHiddenTest extends \PHPUnit_Framework_TestCase
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

        $formHelper = new FormHelperFieldHidden();
        $formHelper->setProperty($propertyObject);
        $formHelper->render();

        $this->assertEquals('<input type="hidden" name="testform[comment]" value="foo" />' . PHP_EOL, $formHelper->render());
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

        $formHelper = new FormHelperFieldHidden();
        $formHelper->setProperty($propertyObject);
        $formHelper->render();

        $this->assertEquals('<input type="hidden" name="testform[0][comment]" value="foo" />' . PHP_EOL, $formHelper->render());
    }

//    public function xssProvider()
//    {
//        return array(
//            array('"xss'),
//            array('\'xss'),
//            array('<xss'),
//            array('>xss'),
//            array('&xss'),
//            array('/xss')
//        );
//    }
//
//    /**
//     * @dataProvider xssProvider
//     */
//    public function testInjection($injection)
//    {
//        $userEntity = new User();
//        $userEntity->setComment($injection);
//
//        $entityContainerMap = $this->getMock('Naucon\Utility\Map');
//        $entityContainerMap->expects($this->any())
//            ->method('count')
//            ->will($this->returnValue(1));
//
//        $form = $this->getMock('Naucon\Form\FormInterface');
//        $form->expects($this->any())
//            ->method('getName')
//            ->will($this->returnValue('testform'));
//        $form->expects($this->any())
//            ->method('getEntityContainerMap')
//            ->will($this->returnValue($entityContainerMap));
//
//        $entityContainer = $this->getMock('Naucon\Form\Mapper\EntityContainerInterface');
//        $entityContainer->expects($this->any())
//            ->method('getForm')
//            ->will($this->returnValue($form));
//
//        $entityContainer->expects($this->any())
//            ->method('getEntity')
//            ->will($this->returnValue($userEntity));
//
//
//        $propertyObject = new Property($entityContainer, 'comment');
//
//        $formHelper = new FormHelperFieldHidden();
//        $formHelper->setProperty($propertyObject);
//        $formHelper->render();
//
//        $this->assertNotEquals('<input type="hidden" name="testform[comment]" value="' . $injection . '" />' . PHP_EOL, $formHelper->render());
//    }
}