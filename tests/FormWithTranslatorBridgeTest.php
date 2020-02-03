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

use Symfony\Component\Finder\Finder;
use Symfony\Component\Translation\Formatter\MessageFormatter;
use Symfony\Component\Translation\Translator as BaseTranslator;
use Symfony\Component\Translation\Loader\XliffFileLoader;
use Symfony\Component\Translation\Loader\YamlFileLoader;
use Naucon\Form\Form;
use Naucon\Form\Configuration;
use Naucon\Form\Validator\Validator;
use Naucon\Form\Translator\TranslatorBridge;
use Naucon\Form\Tests\Entities\User;

class FormWithTranslatorBridgeTest extends \PHPUnit_Framework_TestCase
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
     *
     * @param object $entity          entity a plain old php object
     * @param array  $config          form configuration
     * @param array  $methods         array of getters for the entity
     * @param array  $dataMap         form data
     * @param bool   $expectedIsBound expected form bind result
     * @param bool   $expectedIsValid expected form validation result
     * @param array  $expectedErrors  expected form validation errors
     *
     * @throws \ReflectionException
     */
    public function testFormBind($entity, $config, $methods, $dataMap, $expectedIsBound, $expectedIsValid, $expectedErrors)
    {
        foreach ($methods as $method) {
            $this->assertEquals('', $entity->$method());
        }

		$configPaths = array();
		$configPaths['en_EN'] = __DIR__ . '/Resources/translations/validators.en.yml';
		$configPaths['de_DE'] = __DIR__ . '/Resources/translations/validators.de.yml';

		$translator = new BaseTranslator('de_DE', new MessageFormatter());
		$translator->addLoader('xlf', new XliffFileLoader());
		$translator->addLoader('yaml', new YamlFileLoader());

        // add default translations from symfony validation component
        $dirs = array();
        if (class_exists('Symfony\Component\Validator\Validation')) {
            $r = new \ReflectionClass('Symfony\Component\Validator\Validation');
            $dirs[] = dirname($r->getFileName()).'/Resources/translations';
        }

        // add default translations
        if (is_dir($dir = realpath(__DIR__ . '/../') . '/src/Resources/translations')) {
            $dirs[] = $dir;
        }

        $finder = Finder::create()
            ->files()
            ->filter(function (\SplFileInfo $file) {
                return 2 === substr_count($file->getBasename(), '.') && preg_match('/\.\w+$/', $file->getBasename());
            })
            ->in($dirs)
        ;

        foreach ($finder as $file) {
            list($translationDomain, $locale, $extension) = $foo = explode('.', $file->getBasename(), 3);

            $translator->addResource($extension, $file->getPathname(), $locale, $translationDomain);
        }

        $translatorBridge = new TranslatorBridge($translator);
        $this->assertEquals('de_DE', $translatorBridge->getLocale(), 'unexpected local');

        $configuration = new Configuration($config);
        $validatorBridge = new Validator($configuration, $translatorBridge);

        $form = new Form($entity, 'testform', $configuration);
        $form->setValidator($validatorBridge);
        $form->setTranslator($translatorBridge);

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
        foreach ($expectedErrors as $expectedErrorName => $expectedErrorMessage) {
            $this->assertArrayHasKey($expectedErrorName, $errors, 'form errors do not contain ' . $expectedErrorName);
            $this->assertEquals($expectedErrorMessage, $errors[$expectedErrorName]->getMessage());
        }
    }
}
