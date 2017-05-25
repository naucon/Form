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
use Naucon\Form\Tests\Entities\User;
use Naucon\Form\Translator\Translator;
use Naucon\Form\Validator\Validator;

class FormWithTranslatorTest extends \PHPUnit_Framework_TestCase
{
    public function entityProvider()
    {
        return array(
            array(
                new User(),
                array('locale' => 'de_DE'),
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
                new User(),
                array('locale' => 'de_DE'),
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
                array('username' => 'Dieser Wert sollte nicht leer sein.', 'age' => 'Dieser Wert sollte vom Typ numeric sein.'),
                'invalid dataset user form with missing username and wrong age type'
            ),
            array(
                new User(),
                array('locale' => 'de_DE'),
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
                array('username' => 'Dieser Wert sollte nicht leer sein.'),
                'invalid dataset user form with missing username'
            ),
            array(
                new User(),
                array('locale' => 'de_DE'),
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
                array('username' => 'Dieser Wert sollte nicht leer sein.'),
                'invalid dataset user form with missing form data'
            )
        );
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
    public function testFormBind($entity, $config, $methods, $dataMap, $expectedIsBound, $expectedIsValid, $expectedErrors)
    {
        foreach ($methods as $method) {
            $this->assertEquals('', $entity->$method());
        }

        $configuration = new Configuration($config);

        $translator = new Translator($configuration);
        $validator = new Validator($configuration, $translator);

        $form = new Form($entity, 'testform', $configuration);
        $form->setTranslator($translator);
        $form->setValidator($validator);

        $this->assertEquals('de_DE', $form->getTranslator()->getLocale(), 'unexpected local');

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
            $this->assertEquals($value, $entity->$methods[$key](), 'unexpected form property value of ' . $key . ', getter ' . $methods[$key] . '()');
        }

        $errors = $form->getErrors();

        $this->assertCount(count($expectedErrors), $errors, 'unexpected form error count');
        foreach ($expectedErrors as $expectedErrorName => $expectedErrorMessage) {
            $this->assertArrayHasKey($expectedErrorName, $errors, 'form errors do not contain ' . $expectedErrorName);
            $this->assertEquals($expectedErrorMessage, $errors[$expectedErrorName]->getMessage());
        }
    }
}