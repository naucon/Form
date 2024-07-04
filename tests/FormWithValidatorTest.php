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
use Naucon\Form\Tests\Entities\UserWithConfig;
use Naucon\Form\Tests\Entities\UserWithAnnotation;
use PHPUnit\Framework\TestCase;

class FormWithValidatorTest extends TestCase
{
    public function entityProvider()
    {
        return array(
            array(
                new UserWithConfig(),
                array(),
                array(
                    'username' => 'getUsername',
                    'firstname' => 'getFirstname',
                    'lastname' => 'getLastname',
                    'email' => 'getEmail',
                    'age' => 'getAge'
                ),
                array(
                    'username' => 'max.mustermann',
                    'firstname' => 'Max',
                    'lastname' => 'Mustermann',
                    'email' => 'max.mustermann@yourdomain.com',
                    'age' => '18'
                ),
                true,
                true,
                array(),
                'valid dataset user form'
            ),
            array(
                new UserWithConfig(),
                array(),
                array(
                    'username' => 'getUsername',
                    'firstname' => 'getFirstname',
                    'lastname' => 'getLastname',
                    'email' => 'getEmail',
                    'age' => 'getAge'
                ),
                array(
                    'username' => '',
                    'firstname' => 'Max',
                    'lastname' => 'Mustermann',
                    'email' => 'max.mustermann@yourdomain.com',
                    'age' => 'monday'
                ),
                true,
                false,
                array('username', 'age'),
                'invalid dataset user form with missing username and wrong age type'
            ),
            array(
                new UserWithConfig(),
                array(),
                array(
                    'username' => 'getUsername',
                    'firstname' => 'getFirstname',
                    'lastname' => 'getLastname',
                    'email' => 'getEmail',
                    'age' => 'getAge'
                ),
                array(
                    'email' => 'max.mustermann@yourdomain.com',
                ),
                true,
                false,
                array('username'),
                'invalid dataset user form with missing username'
            ),
            array(
                new UserWithConfig(),
                array(),
                array(
                    'username' => 'getUsername',
                    'firstname' => 'getFirstname',
                    'lastname' => 'getLastname',
                    'email' => 'getEmail',
                    'age' => 'getAge'
                ),
                array(),
                false,
                false,
                array('username'),
                'invalid dataset user form with missing form data'
            )
        );
    }

    /**
     * @dataProvider entityProvider
     * @param object $entity entity a plain old php object
     * @param array $config form configuration
     * @param array $methods array of getters for the entity
     * @param array $dataMap form data
     * @param bool $expectedIsBound expected form bind result
     * @param bool $expectedIsValid expected form validation result
     * @param array $expectedErrors expected form validation errors
     */
    public function testFormBindWithYmlValidator($entity, $config, $methods, $dataMap, $expectedIsBound, $expectedIsValid, $expectedErrors)
    {
        foreach ($methods as $method) {
            $this->assertEquals('', $entity->$method());
        }

        $config['config_paths'] = array(__DIR__ . '/Resources/config/yml/');
        $configuration = new Configuration($config);

        $validator = new Validator($configuration);

        $form = new Form($entity, 'testform', $configuration);
        $form->setValidator($validator);

        $payload = array();
        $payload['_csrf_token'] = $form->getSynchronizerToken();
        if (count($dataMap)) {
            $payload['testform'] = $dataMap;
        }

        $form->bind($payload);

        $this->assertEquals($expectedIsBound, $form->isBound(), 'unexpected form binding result');
        $this->assertEquals($expectedIsValid, $form->isValid(), 'unexpected form validation result');
        if ($expectedIsBound) {
            $this->assertEquals($entity, $form->getBoundEntity(), 'bound entities are not equal');
        }

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

    /**
     * @dataProvider entityProvider
     * @param object $entity entity a plain old php object
     * @param array $config form configuration
     * @param array $methods array of getters for the entity
     * @param array $dataMap form data
     * @param bool $expectedIsBound expected form bind result
     * @param bool $expectedIsValid expected form validation result
     * @param array $expectedErrors expected form validation errors
     */
    public function testFormBindWithXmlValidator($entity, $config, $methods, $dataMap, $expectedIsBound, $expectedIsValid, $expectedErrors)
    {
        foreach ($methods as $method) {
            $this->assertEquals('', $entity->$method());
        }

        $config['config_paths'] = array(__DIR__ . '/Resources/config/xml/');
        $configuration = new Configuration($config);

        $validator = new Validator($configuration);

        $configuration = new Configuration($config);
        $form = new Form($entity, 'testform', $configuration);
        $form->setValidator($validator);

        $payload = array();
        $payload['_csrf_token'] = $form->getSynchronizerToken();
        if (count($dataMap)) {
            $payload['testform'] = $dataMap;
        }

        $form->bind($payload);

        $this->assertEquals($expectedIsBound, $form->isBound(), 'unexpected form binding result');
        $this->assertEquals($expectedIsValid, $form->isValid(), 'unexpected form validation result');
        if ($expectedIsBound) {
            $this->assertEquals($entity, $form->getBoundEntity(), 'bound entities are not equal');
        }

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

    public function entityProviderWithAnnotation()
    {
        return array(
            array(
                new UserWithAnnotation(),
                array(),
                array(
                    'username' => 'getUsername',
                    'firstname' => 'getFirstname',
                    'lastname' => 'getLastname',
                    'email' => 'getEmail',
                    'age' => 'getAge'
                ),
                array(
                    'username' => 'max.mustermann',
                    'firstname' => 'Max',
                    'lastname' => 'Mustermann',
                    'email' => 'max.mustermann@yourdomain.com',
                    'age' => '18'
                ),
                true,
                true,
                array(),
                'valid dataset user form'
            ),
            array(
                new UserWithAnnotation(),
                array(),
                array(
                    'username' => 'getUsername',
                    'firstname' => 'getFirstname',
                    'lastname' => 'getLastname',
                    'email' => 'getEmail',
                    'age' => 'getAge'
                ),
                array(
                    'username' => '',
                    'firstname' => 'Max',
                    'lastname' => 'Mustermann',
                    'email' => 'max.mustermann@yourdomain.com',
                    'age' => 'monday'
                ),
                true,
                false,
                array('username', 'age'),
                'invalid dataset user form with missing username and wrong age type'
            ),
            array(
                new UserWithAnnotation(),
                array(),
                array(
                    'username' => 'getUsername',
                    'firstname' => 'getFirstname',
                    'lastname' => 'getLastname',
                    'email' => 'getEmail',
                    'age' => 'getAge'
                ),
                array(
                    'email' => 'max.mustermann@yourdomain.com',
                ),
                true,
                false,
                array('username'),
                'invalid dataset user form with missing username'
            ),
            array(
                new UserWithAnnotation(),
                array(),
                array(
                    'username' => 'getUsername',
                    'firstname' => 'getFirstname',
                    'lastname' => 'getLastname',
                    'email' => 'getEmail',
                    'age' => 'getAge'
                ),
                array(),
                false,
                false,
                array('username'),
                'invalid dataset user form with missing form data'
            )
        );
    }

    /**
     * @dataProvider entityProviderWithAnnotation
     * @param object $entity entity a plain old php object
     * @param array $config form configuration
     * @param array $methods array of getters for the entity
     * @param array $dataMap form data
     * @param bool $expectedIsBound expected form bind result
     * @param bool $expectedIsValid expected form validation result
     * @param array $expectedErrors expected form validation errors
     */
    public function testFormBindWithAnnotationValidator($entity, $config, $methods, $dataMap, $expectedIsBound, $expectedIsValid, $expectedErrors)
    {
        foreach ($methods as $method) {
            $this->assertEquals('', $entity->$method());
        }

        $configuration = new Configuration($config);
        $validator = new Validator($configuration);

        $form = new Form($entity, 'testform', $configuration);
        $form->setValidator($validator);

        $payload = array();
        $payload['_csrf_token'] = $form->getSynchronizerToken();
        if (count($dataMap)) {
            $payload['testform'] = $dataMap;
        }

        $form->bind($payload);

        $this->assertEquals($expectedIsBound, $form->isBound(), 'unexpected form binding result');
        $this->assertEquals($expectedIsValid, $form->isValid(), 'unexpected form validation result');
        if ($expectedIsBound) {
            $this->assertEquals($entity, $form->getBoundEntity(), 'bound entities are not equal');
        }

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
}
