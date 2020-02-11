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

use Naucon\Form\Form;
use Naucon\Form\Configuration;
use Naucon\Form\Validator\Validator;
use Naucon\Form\Tests\Entities\User;
use Naucon\Form\Tests\Entities\Product;
use Naucon\Form\Tests\Entities\Address;

class FormTest extends \PHPUnit_Framework_TestCase
{
    public function entityProvider()
    {
        return [
            [
                new User(),
                [],
                [
                    'username' => 'getUsername',
                    'firstname' => 'getFirstname',
                    'lastname' => 'getLastname',
                    'email' => 'getEmail',
                    'age' => 'getAge'
                ],
                [
                    'username' => 'max.mustermann',
                    'firstname' => 'Max',
                    'lastname' => 'Mustermann',
                    'email' => 'max.mustermann@yourdomain.com',
                    'age' => '18'
                ],
                true,
                true,
                [],
                'valid dataset user form'
            ],
            [
                new User(),
                [],
                [
                    'username' => 'getUsername',
                    'firstname' => 'getFirstname',
                    'lastname' => 'getLastname',
                    'email' => 'getEmail',
                    'age' => 'getAge'
                ],
                [
                    'username' => '',
                    'firstname' => 'Max',
                    'lastname' => 'Mustermann',
                    'email' => 'max.mustermann@yourdomain.com',
                    'age' => 'monday'
                ],
                true,
                false,
                ['username', 'age'],
                'invalid dataset user form with missing username and wrong age type'
            ],
            [
                new User(),
                [],
                [
                    'username' => 'getUsername',
                    'firstname' => 'getFirstname',
                    'lastname' => 'getLastname',
                    'email' => 'getEmail',
                    'age' => 'getAge'
                ],
                [
                    'email' => 'max.mustermann@yourdomain.com'
                ],
                true,
                false,
                ['username'],
                'invalid dataset user form with missing username'
            ],
            [
                new User(),
                [],
                [
                    'username' => 'getUsername',
                    'firstname' => 'getFirstname',
                    'lastname' => 'getLastname',
                    'email' => 'getEmail',
                    'age' => 'getAge'
                ],
                [],
                false,
                false,
                ['username'],
                'invalid dataset user form with missing form data'
            ],
            [
                new Address(),
                [],
                [
                    'street_name' => 'getStreetName',
                    'street_number' => 'getStreetNumber',
                    'postal_code' => 'getPostalCode',
                    'town' => 'getTown',
                    'country' => 'getCountry'
                ],
                [
                    'street_name' => 'Any-Street',
                    'street_number' => '1',
                    'postal_code' => '12345',
                    'town' => 'Anywhere',
                    'country' => 'Anyland'
                ],
                true,
                false,
                ['postal_code'],
                'invalid dataset address form with postvalidatorHook contrain violation',
            ],
            [
                new Address(),
                [],
                [
                    'street_name' => 'getStreetName',
                    'street_number' => 'getStreetNumber',
                    'postal_code' => 'getPostalCode',
                    'town' => 'getTown',
                    'country' => 'getCountry'
                ],
                [
                    'street_name' => 'Any-Street',
                    'street_number' => '1',
                    'postal_code' => '54321',
                    'town' => 'Anywhere',
                    'country' => 'Anyland'
                ],
                true,
                true,
                [],
                'valid dataset address form',
            ]
        ];
    }

    /**
     * @dataProvider entityProvider
     * @param    object     $entity                 entity a plain old php object
     * @param	 array		$config			        form configuration
     * @param    array      $methods                array of getters for the entity
     * @param    array      $dataMap                form data
     * @param    bool       $expectedIsBound        expected form bind result
     * @param    bool       $expectedIsValid        expected form validation result
     * @param    array      $expectedErrors         expected form validation errors
     */
    public function testFormBind($entity, $config, $methods, $dataMap, $expectedIsBound, $expectedIsValid, $expectedErrors)
    {
        foreach ($methods as $method) {
            $this->assertEquals('', $entity->$method());
        }

        $configuration = new Configuration($config);
        $validator = new Validator($configuration);

        $form = new Form($entity, 'testform', $configuration);
        $form->setValidator($validator);
        $form->bind();

        $payload = array();
        $payload['_csrf_token'] = $form->getSynchronizerToken();
        if (count($dataMap)) {
            $payload['testform'] = $dataMap;
        }

        $form = new Form($entity, 'testform', $configuration);
        $form->setValidator($validator);
        $form->bind($payload);

        $this->assertEquals($expectedIsBound, $form->isBound(), 'unexpected form binding result');
        $this->assertEquals($expectedIsValid, $form->isValid(), 'unexpected form validation result');
        if ($expectedIsBound) {
            $this->assertEquals($entity, $form->getBoundEntity(), 'bound entities are not equal');
        }
        $this->assertEquals('TOKEN', $form->getSynchronizerToken(), 'unexpected csrf token');

        foreach ($dataMap as $key => $value) {
            $methodName = $methods[$key];
            $actualValue = $entity->$methodName();
            $this->assertEquals($value, $actualValue, 'unexpected form property value of ' . $key . ', getter ' . $methodName . '()');
        }

        $errors = $form->getErrors();

        $this->assertCount(count($expectedErrors), $errors, 'unexpected form error count');
        foreach ($expectedErrors as $expectedError) {
            $this->assertArrayHasKey($expectedError, $errors, 'form errors do not contain ' . $expectedError);
        }
    }

    public function wrongEntityProvider()
    {
        return array(
            array(
                'aString',
                1234,
                0.876543,
                array(new Product(), new Product()),
                array(new User())
            )
        );
    }

    /**
     * @dataProvider wrongEntityProvider
     * @expectedException \Naucon\Form\Exception\InvalidArgumentException
     */
    public function testFormWithInvalidEntity($entity)
    {
        $configuration = new Configuration();

        $form = new Form($entity, 'testform', $configuration);
    }

    /**
     * @dataProvider entityProvider
     * @param    object     $entity             entity a plain old php object
     * @param	 array		$config			    form configuration
     * @param    array      $methods            array of getters for the entity
     * @param    array      $dataMap            form data
     * @param    bool       $expectedIsBound    expected form bind result
     * @param    bool       $expectedIsValid    expected form validation result
     * @param    array      $expectedErrors     expected form validation errors
     */
    public function testFormBindWithInvaildToken($entity, $config, $methods, $dataMap, $expectedIsBound, $expectedIsValid, $expectedErrors)
    {
        foreach ($methods as $method) {
            $this->assertEquals('', $entity->$method());
        }

        $configuration = new Configuration($config);
        $validator = new Validator($configuration);

        $form = new Form($entity, 'testform', $configuration);
        $form->setValidator($validator);
        $form->bind();

        $payload = array();
        $payload['testform'] = $dataMap;
        $payload['_csrf_token'] = 'invalid token';

        $form = new Form($entity, 'testform', $configuration);
        $form->setValidator($validator);
        $form->bind($payload);

        $this->assertFalse($form->isBound(), 'unexpected form binding result, should be false because of invalid token');
    }

    /**
     * @dataProvider entityProvider
     * @param    object     $entity             entity a plain old php object
     * @param	 array		$config			    form configuration
     * @param    array      $methods            array of getters for the entity
     * @param    array      $dataMap            form data
     * @param    bool       $expectedIsBound    expected form bind result
     * @param    bool       $expectedIsValid    expected form validation result
     * @param    array      $expectedErrors     expected form validation errors
     */
    public function testFormBindWithTokenParameter($entity, $config, $methods, $dataMap, $expectedIsBound, $expectedIsValid, $expectedErrors)
    {
        $config = array_replace($config, array('csrf_parameter' => '_foo_csrf_token'));

        $configuration = new Configuration($config);
        $validator = new Validator($configuration);

        $form = new Form($entity, 'testform', $configuration);
        $form->setValidator($validator);

        $payload = array();
        $payload['_foo_csrf_token'] = $form->getSynchronizerToken();
        if (count($dataMap)) {
            $payload['testform'] = $dataMap;
        }

        $form->bind($payload);

        $this->assertEquals($expectedIsBound, $form->isBound(), 'unexpected form binding result');
    }
}
