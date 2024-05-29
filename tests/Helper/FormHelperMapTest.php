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

use Naucon\Form\Form;
use Naucon\Form\Configuration;
use Naucon\Form\Mapper\EntityContainerInterface;
use Naucon\Form\Helper\FormHelperMap;
use Naucon\Form\Tests\Entities\User;
use PHPUnit\Framework\TestCase;

class FormHelperMapTest extends TestCase
{
    /**
     * @return    EntityContainerInterface
     */
    public function testInitFormEntity()
    {
        $userEntity = new User();
        $userEntity->setUsername('max.mustermann');
        $userEntity->setFirstname('Max');
        $userEntity->setLastname('Mustermann');
        $userEntity->setEmail('max.mustermann@yourdomain.com');
        $userEntity->setAge(18);
        $userEntity->setComment('foo');

        $configuration = new Configuration();
        $form = new Form($userEntity, 'testform', $configuration);
        $entityContainer = $form->getFirstEntityContainer();

        $this->assertNotNull($entityContainer);

        return $entityContainer;
    }

    /**
     * @depends   testInitFormEntity
     * @param     EntityContainerInterface      $entityContainer        form entity container
     */
    public function testLoad(EntityContainerInterface $entityContainer)
    {
        $formHelperMap = new FormHelperMap();

        $propertyObject = $entityContainer->getProperty('username');
        $formHelperRenderString = $formHelperMap->loadField($propertyObject, 'text');
        $this->assertEquals('<input type="text" name="testform[username]" value="max.mustermann" id="testform_username" />' . PHP_EOL, $formHelperRenderString);

        $propertyObject = $entityContainer->getProperty('newsletter');
        $formHelperRenderString = $formHelperMap->loadChoice($propertyObject, 'checkbox', 1);
        $this->assertEquals('<input type="checkbox" name="testform[newsletter]" value="1" id="testform_newsletter" />' . PHP_EOL, $formHelperRenderString);

        $formHelperRenderString = $formHelperMap->loadChoice($propertyObject, 'radio', 1);
        $this->assertEquals('<input type="radio" name="testform[newsletter]" value="1" id="testform_newsletter" />' . PHP_EOL, $formHelperRenderString);

        $propertyObject = $entityContainer->getProperty('comment');
        $formHelperRenderString = $formHelperMap->loadField($propertyObject, 'textarea');
        $this->assertEquals('<textarea name="testform[comment]" id="testform_comment">foo</textarea>' . PHP_EOL, $formHelperRenderString);
    }

    /**
     * @depends   testInitFormEntity
     * @param     EntityContainerInterface      $entityContainer        form entity container
     */
    public function testAttacheHelper(EntityContainerInterface $entityContainer)
    {
        $propertyObject = $entityContainer->getProperty('username');

        $formHelperMap = new FormHelperMap();
        $formHelperMap->attachHelper('foo', new FormHelperFieldFoo());
        $formHelperMap->attachHelper('bar', new FormHelperFieldBar());

        $formHelperRenderString = $formHelperMap->loadField($propertyObject, 'foo');
        $this->assertEquals('<foo name="testform[username]" />' . PHP_EOL, $formHelperRenderString);

        $formHelperRenderString = $formHelperMap->loadField($propertyObject, 'bar');
        $this->assertEquals('<bar name="testform[username]" />' . PHP_EOL, $formHelperRenderString);

        $formHelperRenderString = $formHelperMap->loadField($propertyObject, 'text');
        $this->assertEquals('<input type="text" name="testform[username]" value="max.mustermann" id="testform_username" />' . PHP_EOL, $formHelperRenderString);
    }
}
