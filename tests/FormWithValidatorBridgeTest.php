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
use Naucon\Form\Validator\ValidatorBridge;
use Naucon\Form\Tests\Entities\UserWithConfig;
use Naucon\Form\Tests\Entities\UserWithAnnotation;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Validation;

class FormWithValidatorBridgeTest extends TestCase
{
    public function entityProvider()
    {
        return [
            [
                new UserWithConfig(),
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
                new UserWithConfig(),
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
                new UserWithConfig(),
                [],
                [
                    'username' => 'getUsername',
                    'firstname' => 'getFirstname',
                    'lastname' => 'getLastname',
                    'email' => 'getEmail',
                    'age' => 'getAge'
                ],
                [
                    'email' => 'max.mustermann@yourdomain.com',
                ],
                true,
                false,
                ['username'],
                'invalid dataset user form with missing username'
            ],
            [
                new UserWithConfig(),
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
            ]
        ];
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

        $configPaths = [__DIR__ . '/Resources/config/yml/validation.yml'];

        $handler = Validation::createValidatorBuilder()
            ->addYamlMappings($configPaths)
            ->getValidator();

        $validatorBridge = new ValidatorBridge($handler);

        $configuration = new Configuration($config);

        $form = new Form($entity, 'testform', $configuration);
        $form->setValidator($validatorBridge);

        $payload = [];
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

        $configPaths = [__DIR__ . '/Resources/config/xml/validation.xml'];

        $handler = Validation::createValidatorBuilder()
            ->addXmlMappings($configPaths)
            ->getValidator();

        $validatorBridge = new ValidatorBridge($handler);

        $configuration = new Configuration($config);

        $form = new Form($entity, 'testform', $configuration);
        $form->setValidator($validatorBridge);

        $payload = [];
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
        return [
            [
                new UserWithAnnotation(),
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
                new UserWithAnnotation(),
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
                new UserWithAnnotation(),
                [],
                [
                    'username' => 'getUsername',
                    'firstname' => 'getFirstname',
                    'lastname' => 'getLastname',
                    'email' => 'getEmail',
                    'age' => 'getAge'
                ],
                [
                    'email' => 'max.mustermann@yourdomain.com',
                ],
                true,
                false,
                ['username'],
                'invalid dataset user form with missing username'
            ],
            [
                new UserWithAnnotation(),
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
            ]
        ];
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

        $handler = Validation::createValidatorBuilder()
            ->enableAttributeMapping()
            ->addDefaultDoctrineAnnotationReader()
            ->getValidator();

        $validatorBridge = new ValidatorBridge($handler);

        $configuration = new Configuration($config);

        $form = new Form($entity, 'testform', $configuration);
        $form->setValidator($validatorBridge);

        $payload = [];
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
