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

use Naucon\Form\FormManager;
use Naucon\Form\FormInterface;
use Naucon\Form\FormCollectionInterface;
use Naucon\Form\Security\SynchronizerTokenNull;
use Naucon\Form\Security\SynchronizerTokenInterface;
use Naucon\Form\Translator\TranslatorNull;
use Naucon\Form\Translator\TranslatorInterface;
use Naucon\Form\Tests\Entities\User;
use Naucon\Form\Tests\Entities\Product;
use Naucon\Form\Tests\Entities\Address;
use Naucon\Form\Tests\Entities\CreditCard;
use Naucon\Form\Tests\Entities\DirectDebit;
use PHPUnit\Framework\TestCase;

class FormManagerTest extends TestCase
{
    /**
     * @var SynchronizerTokenInterface
     */
    protected $synchronizerToken;

    /**
     * @var TranslatorInterface
     */
    protected $translator;



    protected function setUp()
    {
        $this->synchronizerToken = new SynchronizerTokenNull();
        $this->translator = new TranslatorNull();
    }

    public function entityProvider()
    {
        return array(
            array(
                new User(),
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
                new User(),
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
                new User(),
                array(),
                array(
                    'username' => 'getUsername',
                    'firstname' => 'getFirstname',
                    'lastname' => 'getLastname',
                    'email' => 'getEmail',
                    'age' => 'getAge'
                ),
                array(
                    'email' => 'max.mustermann@yourdomain.com'
                ),
                true,
                false,
                array('username'),
                'invalid dataset user form with missing username'
            ),
            array(
                new User(),
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
            ),
            array(
                new Address(),
                array(),
                array(
                    'street_name' => 'getStreetName',
                    'street_number' => 'getStreetNumber',
                    'postal_code' => 'getPostalCode',
                    'town' => 'getTown',
                    'country' => 'getCountry'
                ),
                array(
                    'street_name' => 'Any-Street',
                    'street_number' => '1',
                    'postal_code' => '12345',
                    'town' => 'Anywhere',
                    'country' => 'Anyland'
                ),
                true,
                false,
                array('postal_code'),
                'invalid dataset address form with postvalidatorHook contrain violation',
            ),
            array(
                new Address(),
                array(),
                array(
                    'street_name' => 'getStreetName',
                    'street_number' => 'getStreetNumber',
                    'postal_code' => 'getPostalCode',
                    'town' => 'getTown',
                    'country' => 'getCountry'
                ),
                array(
                    'street_name' => 'Any-Street',
                    'street_number' => '1',
                    'postal_code' => '54321',
                    'town' => 'Anywhere',
                    'country' => 'Anyland'
                ),
                true,
                true,
                array(),
                'valid dataset address form',
            )
        );
    }

    /**
     * @dataProvider entityProvider
     * @param    object     $entity                 entity of a plain old php object
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

        $formManager = new FormManager();
        $formManager->setSynchronizerToken($this->synchronizerToken);
        $form = $formManager->createForm($entity, 'testform', $config);
        $form->bind();

        $this->assertInstanceOf(FormInterface::class, $form);

        $payload = array();
        $payload['_csrf_token'] = $form->getSynchronizerToken();
        if (count($dataMap)) {
            $payload['testform'] = $dataMap;
        }

        $form = $formManager->createForm($entity, 'testform', $config);
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

    public function entitiesProvider()
    {
        return array(
            array(
                array(new Product(), new Product(), new Product()),
                array(),
                array(
                    'product_id' => 'getProductId',
                    'product_number' => 'getProductNumber',
                    'product_desc' => 'getProductDesc',
                    'price' => 'getPrice'
                ),
                array(
                    array(
                        'product_number' => 'V001',
                        'product_desc' => 'Apple',
                        'price' => '9.95'
                    ),
                    array(
                        'product_number' => 'V002',
                        'product_desc' => 'Banana',
                        'price' => '9.95'
                    ),
                    array(
                        'product_number' => 'V003',
                        'product_desc' => 'Orange',
                        'price' => '9.95'
                    )
                ),
                true,
                true,
                array(),
                'valid dataset product forms',
            ),
            array(
                array(new Product(), new Product(), new Product()),
                array(),
                array(
                    'product_id' => 'getProductId',
                    'product_number' => 'getProductNumber',
                    'product_desc' => 'getProductDesc',
                    'price' => 'getPrice'
                ),
                array(
                    array(
                        'product_number' => '',
                        'product_desc' => 'Apple',
                        'price' => '9.95'
                    ),
                    array(
                        'product_number' => 'V002',
                        'product_desc' => '',
                        'price' => '7.95'
                    ),
                    array(
                        'product_number' => 'V003',
                        'product_desc' => 'Orange',
                        'price' => 'free'
                    )
                ),
                true,
                false,
                array(
                    array('product_number'),
                    array('product_desc'),
                    array('price')
                ),
                'invalid dataset product forms with missing product number and wrong price type',
            ),
            array(
                array(new Product(), new Product(), new Product()),
                array(),
                array(
                    'product_id' => 'getProductId',
                    'product_number' => 'getProductNumber',
                    'product_desc' => 'getProductDesc',
                    'price' => 'getPrice'
                ),
                array(),
                false,
                false,
                array(
                    array('product_number', 'product_desc', 'price'),
                    array('product_number', 'product_desc', 'price'),
                    array('product_number', 'product_desc', 'price')
                ),
                'invalid dataset product forms with missing form data',
            ),
            array(
                array(new Product(), new Product(), new Product()),
                array(),
                array(
                    'product_id' => 'getProductId',
                    'product_number' => 'getProductNumber',
                    'product_desc' => 'getProductDesc',
                    'price' => 'getPrice'
                ),
                array(
                    array(
                        'product_number' => 'V001',
                        'product_desc' => 'Apple',
                        'price' => '9.95'
                    )
                ),
                false,
                false,
                array(
                    1 => array('product_number', 'product_desc', 'price'),
                    2 => array('product_number', 'product_desc', 'price')
                ),
                'invalid dataset product forms with missing second and third product',
            ),
        );
    }

    /**
     * @dataProvider entitiesProvider
     * @param    array      $entities               array of entities (plain old php objects)
     * @param	 array		$config			        form configuration
     * @param    array      $methods                array of getters for the entity
     * @param    array      $dataMap                form data
     * @param    bool       $expectedIsBound        expected form bind result
     * @param    bool       $expectedIsValid        expected form validation result
     * @param    array      $expectedErrors         expected form validation errors
     */
    public function testFormBinds($entities, $config, $methods, $dataMap, $expectedIsBound, $expectedIsValid, $expectedErrors)
    {
        $formManager = new FormManager();
        $formManager->setSynchronizerToken($this->synchronizerToken);
        $form = $formManager->createFormCollection($entities, 'testforms', $config);
        $form->bind();

        $this->assertInstanceOf(FormCollectionInterface::class, $form);

        $payload = array();
        $payload['testforms'] = $dataMap;
        $payload['_csrf_token'] = $form->getSynchronizerToken();

        $form = $formManager->createFormCollection($entities, 'testforms', $config);
        $form->bind($payload);

        $this->assertEquals($expectedIsBound, $form->isBound(), 'unexpected form binding result');
        $this->assertEquals($expectedIsValid, $form->isValid(), 'unexpected form validation result');
        if ($expectedIsBound) {
            $this->assertEquals($entities, $form->getBoundEntities(), 'bound entities are not equal');
        }
        $this->assertEquals('TOKEN', $form->getSynchronizerToken(), 'unexpected csrf token');

        foreach ($dataMap as $entityName => $entityPayload) {
            foreach ($entityPayload as $key => $value) {
                $methodName = $methods[$key];
                $this->assertEquals($value, $entities[$entityName]->$methodName(), 'unexpected form property value of ' . $key . ', getter ' . $methodName . '()');
            }
        }

        $errors = $form->getErrors();

        $this->assertCount(count($expectedErrors), $errors, 'unexpected form error count');
        foreach ($expectedErrors as $entityName => $expectedEntityErrors) {
            $this->assertCount(count($expectedEntityErrors), $errors[$entityName], 'unexpected form error count for entity ' . $entityName);
            foreach ($expectedEntityErrors as $expectedEntityError) {
                $this->assertArrayHasKey($expectedEntityError, $errors[$entityName], 'form errors do not contain ' . $expectedEntityError);
            }
        }
    }

    public function entityCollectionProvider()
    {
        return array(
            array(
                array('cc' => new CreditCard(), 'dd' => new DirectDebit()),
                array('collection_type' => FormCollectionInterface::COLLECTION_TYPE_ALL),
                array(
                    'cc' => array(
                        'card_brand' => 'getCardBrand',
                        'card_holder_name' => 'getCardHolderName',
                        'card_number' => 'getCardNumber',
                        'expiration_date' => 'getExpirationDate'
                    ),
                    'dd' => array(
                        'account_holder_name' => 'getAccountHolderName',
                        'iban' => 'getIban',
                        'bic' => 'getBic',
                        'bank' => 'getBank'
                    )
                ),
                array(
                    'cc' => array(
                        'card_brand' => 'VISA',
                        'card_holder_name' => 'Max Mustermann',
                        'card_number' => '4111111111111111',
                        'expiration_date' => '12-2015'
                    ),
                    'dd' => array(
                        'account_holder_name' => 'Max Mustermann',
                        'iban' => 'DE00210501700012345678',
                        'bic' => 'MARKDEFF',
                        'bank' => 'Bundesbank'
                    )
                ),
                true,
                true,
                array(),
                '#0 valid dataset form collection of type ALL'
            ),
            array(
                array('cc' => new CreditCard(), 'dd' => new DirectDebit()),
                array('collection_type' => FormCollectionInterface::COLLECTION_TYPE_ALL),
                array(
                    'cc' => array(
                        'card_brand' => 'getCardBrand',
                        'card_holder_name' => 'getCardHolderName',
                        'card_number' => 'getCardNumber',
                        'expiration_date' => 'getExpirationDate'
                    ),
                    'dd' => array(
                        'account_holder_name' => 'getAccountHolderName',
                        'iban' => 'getIban',
                        'bic' => 'getBic',
                        'bank' => 'getBank'
                    )
                ),
                array(
                    'cc' => array(
                        'card_brand' => 'VISA',
                        'card_holder_name' => 'Max Mustermann',
                        'card_number' => '4111111111111111',
                        'expiration_date' => '12-2015'
                    )
                ),
                false,
                false,
                array('dd' => array('account_holder_name', 'iban', 'bic', 'bank')),
                '#1 invalid dataset form collection of type ALL with missing dd form data'
            ),
            array(
                array('cc' => new CreditCard(), 'dd' => new DirectDebit()),
                array('collection_type' => FormCollectionInterface::COLLECTION_TYPE_ONE),
                array(
                    'cc' => array(
                        'card_brand' => 'getCardBrand',
                        'card_holder_name' => 'getCardHolderName',
                        'card_number' => 'getCardNumber',
                        'expiration_date' => 'getExpirationDate'
                    ),
                    'dd' => array(
                        'account_holder_name' => 'getAccountHolderName',
                        'iban' => 'getIban',
                        'bic' => 'getBic',
                        'bank' => 'getBank'
                    )
                ),
                array(
                    'ncform_option' => 'dd',
                    'cc' => array(
                        'card_brand' => 'VISA',
                        'card_holder_name' => 'Max Mustermann',
                        'card_number' => '4111111111111111',
                        'expiration_date' => '12-2015'
                    ),
                    'dd' => array(
                        'account_holder_name' => 'Max Mustermann',
                        'iban' => 'DE00210501700012345678',
                        'bic' => 'MARKDEFF',
                        'bank' => 'Bundesbank'
                    )
                ),
                true,
                true,
                array(),
                '#2 valid dataset form collection of type ONE with option dd and valid form data of cc and dd'
            ),
            array(
                array('cc' => new CreditCard(), 'dd' => new DirectDebit()),
                array('collection_type' => FormCollectionInterface::COLLECTION_TYPE_ONE),
                array(
                    'cc' => array(
                        'card_brand' => 'getCardBrand',
                        'card_holder_name' => 'getCardHolderName',
                        'card_number' => 'getCardNumber',
                        'expiration_date' => 'getExpirationDate'
                    ),
                    'dd' => array(
                        'account_holder_name' => 'getAccountHolderName',
                        'iban' => 'getIban',
                        'bic' => 'getBic',
                        'bank' => 'getBank'
                    )
                ),
                array(
                    'ncform_option' => 'dd',
                    'cc' => array(),
                    'dd' => array(
                        'account_holder_name' => 'Max Mustermann',
                        'iban' => 'DE00210501700012345678',
                        'bic' => 'MARKDEFF',
                        'bank' => 'Bundesbank'
                    )
                ),
                true,
                true,
                array(),
                '#3 valid dataset form collection of type ONE with option dd and invalid form data for cc'
            ),
            array(
                array('cc' => new CreditCard(), 'dd' => new DirectDebit()),
                array('collection_type' => FormCollectionInterface::COLLECTION_TYPE_ONE),
                array(
                    'cc' => array(
                        'card_brand' => 'getCardBrand',
                        'card_holder_name' => 'getCardHolderName',
                        'card_number' => 'getCardNumber',
                        'expiration_date' => 'getExpirationDate'
                    ),
                    'dd' => array(
                        'account_holder_name' => 'getAccountHolderName',
                        'iban' => 'getIban',
                        'bic' => 'getBic',
                        'bank' => 'getBank'
                    )
                ),
                array(
                    'ncform_option' => 'dd',
                    'dd' => array(
                        'account_holder_name' => 'Max Mustermann',
                        'iban' => 'DE00210501700012345678',
                        'bic' => 'MARKDEFF',
                        'bank' => 'Bundesbank'
                    )
                ),
                true,
                true,
                array(),
                '#4 valid dataset form collection of type ONE with option dd and missing form data for cc'
            ),
            array(
                array('cc' => new CreditCard(), 'dd' => new DirectDebit()),
                array('collection_type' => FormCollectionInterface::COLLECTION_TYPE_ONE),
                array(
                    'cc' => array(
                        'card_brand' => 'getCardBrand',
                        'card_holder_name' => 'getCardHolderName',
                        'card_number' => 'getCardNumber',
                        'expiration_date' => 'getExpirationDate'
                    ),
                    'dd' => array(
                        'account_holder_name' => 'getAccountHolderName',
                        'iban' => 'getIban',
                        'bic' => 'getBic',
                        'bank' => 'getBank'
                    )
                ),
                array(
                    'ncform_option' => 'dd',
                    'cc' => array(
                        'card_brand' => 'VISA',
                        'card_holder_name' => 'Max Mustermann',
                        'card_number' => '4111111111111111',
                        'expiration_date' => '12-2015'
                    )
                ),
                false,
                false,
                array(
                    'dd' => array('account_holder_name', 'iban', 'bic', 'bank')
                ),
                '#5 invalid dataset form collection of type ONE with option dd and missing from data of dd'
            ),
            array(
                array('cc' => new CreditCard(), 'dd' => new DirectDebit()),
                array('collection_type' => FormCollectionInterface::COLLECTION_TYPE_MANY),
                array(
                    'cc' => array(
                        'card_brand' => 'getCardBrand',
                        'card_holder_name' => 'getCardHolderName',
                        'card_number' => 'getCardNumber',
                        'expiration_date' => 'getExpirationDate'
                    ),
                    'dd' => array(
                        'account_holder_name' => 'getAccountHolderName',
                        'iban' => 'getIban',
                        'bic' => 'getBic',
                        'bank' => 'getBank'
                    )
                ),
                array(
                    'ncform_option' => array('cc', 'dd'),
                    'cc' => array(
                        'card_brand' => 'VISA',
                        'card_holder_name' => 'Max Mustermann',
                        'card_number' => '4111111111111111',
                        'expiration_date' => '12-2015'
                    ),
                    'dd' => array(
                        'account_holder_name' => 'Max Mustermann',
                        'iban' => 'DE00210501700012345678',
                        'bic' => 'MARKDEFF',
                        'bank' => 'Bundesbank'
                    )
                ),
                true,
                true,
                array(),
                '#6 valid dataset form collection of type MANY with option cc and dd'
            ),
            array(
                array('cc' => new CreditCard(), 'dd' => new DirectDebit()),
                array('collection_type' => FormCollectionInterface::COLLECTION_TYPE_MANY),
                array(
                    'cc' => array(
                        'card_brand' => 'getCardBrand',
                        'card_holder_name' => 'getCardHolderName',
                        'card_number' => 'getCardNumber',
                        'expiration_date' => 'getExpirationDate'
                    ),
                    'dd' => array(
                        'account_holder_name' => 'getAccountHolderName',
                        'iban' => 'getIban',
                        'bic' => 'getBic',
                        'bank' => 'getBank'
                    )
                ),
                array(
                    'ncform_option' => array('dd'),
                    'cc' => array(
                        'card_brand' => 'VISA',
                        'card_holder_name' => 'Max Mustermann',
                        'card_number' => '4111111111111111',
                        'expiration_date' => '12-2015'
                    ),
                    'dd' => array(
                        'account_holder_name' => 'Max Mustermann',
                        'iban' => 'DE00210501700012345678',
                        'bic' => 'MARKDEFF',
                        'bank' => 'Bundesbank'
                    )
                ),
                true,
                true,
                array(),
                '#7 valid dataset form collection of type MANY with option dd and form data of cc and dd'
            ),
            array(
                array('cc' => new CreditCard(), 'dd' => new DirectDebit()),
                array('collection_type' => FormCollectionInterface::COLLECTION_TYPE_MANY),
                array(
                    'cc' => array(
                        'card_brand' => 'getCardBrand',
                        'card_holder_name' => 'getCardHolderName',
                        'card_number' => 'getCardNumber',
                        'expiration_date' => 'getExpirationDate'
                    ),
                    'dd' => array(
                        'account_holder_name' => 'getAccountHolderName',
                        'iban' => 'getIban',
                        'bic' => 'getBic',
                        'bank' => 'getBank'
                    )
                ),
                array(
                    'ncform_option' => array('dd'),
                    'dd' => array(
                        'account_holder_name' => 'Max Mustermann',
                        'iban' => 'DE00210501700012345678',
                        'bic' => 'MARKDEFF',
                        'bank' => 'Bundesbank'
                    )
                ),
                true,
                true,
                array(),
                '#8 valid dataset form collection of type MANY with option dd and without form data of cc'
            ),
            array(
                array('cc' => new CreditCard(), 'dd' => new DirectDebit()),
                array('collection_type' => FormCollectionInterface::COLLECTION_TYPE_MANY),
                array(
                    'cc' => array(
                        'card_brand' => 'getCardBrand',
                        'card_holder_name' => 'getCardHolderName',
                        'card_number' => 'getCardNumber',
                        'expiration_date' => 'getExpirationDate'
                    ),
                    'dd' => array(
                        'account_holder_name' => 'getAccountHolderName',
                        'iban' => 'getIban',
                        'bic' => 'getBic',
                        'bank' => 'getBank'
                    )
                ),
                array(
                    'ncform_option' => array('cc', 'dd'),
                    'cc' => array(),
                    'dd' => array()
                ),
                true,
                false,
                array(
                    'cc' => array('card_brand', 'card_holder_name', 'card_number', 'expiration_date'),
                    'dd' => array('account_holder_name', 'iban', 'bic', 'bank'),
                ),
                '#9 invalid dataset form collection of type MANY with option cc and dd and missing form data for cc and dd'
            ),
            array(
                array('cc' => new CreditCard(), 'dd' => new DirectDebit()),
                array('collection_type' => FormCollectionInterface::COLLECTION_TYPE_MANY),
                array(
                    'cc' => array(
                        'card_brand' => 'getCardBrand',
                        'card_holder_name' => 'getCardHolderName',
                        'card_number' => 'getCardNumber',
                        'expiration_date' => 'getExpirationDate'
                    ),
                    'dd' => array(
                        'account_holder_name' => 'getAccountHolderName',
                        'iban' => 'getIban',
                        'bic' => 'getBic',
                        'bank' => 'getBank'
                    )
                ),
                array(
                    'ncform_option' => array('cc', 'dd'),
                    'cc' => array(
                        'card_brand' => 'VISA',
                        'card_holder_name' => 'Max Mustermann',
                        'card_number' => '4111111111111111',
                        'expiration_date' => '12-2015'
                    ),
                    'dd' => array()
                ),
                true,
                false,
                array('dd' => array('account_holder_name', 'iban', 'bic', 'bank')),
                '#10 invalid dataset form collection of type MANY with option cc and dd and missing form data for dd'
            ),
            array(
                array('cc' => new CreditCard(), 'dd' => new DirectDebit()),
                array('collection_type' => FormCollectionInterface::COLLECTION_TYPE_MANY),
                array(
                    'cc' => array(
                        'card_brand' => 'getCardBrand',
                        'card_holder_name' => 'getCardHolderName',
                        'card_number' => 'getCardNumber',
                        'expiration_date' => 'getExpirationDate'
                    ),
                    'dd' => array(
                        'account_holder_name' => 'getAccountHolderName',
                        'iban' => 'getIban',
                        'bic' => 'getBic',
                        'bank' => 'getBank'
                    )
                ),
                array(
                    'ncform_option' => array('cc', 'dd'),
                ),
                false,
                false,
                array(
                    'cc' => array('card_brand', 'card_holder_name', 'card_number', 'expiration_date'),
                    'dd' => array('account_holder_name', 'iban', 'bic', 'bank'),
                ),
                '#9 invalid dataset form collection of type MANY with option cc and dd and missing form data for cc and dd'
            ),
            array(
                array('cc' => new CreditCard(), 'dd' => new DirectDebit()),
                array('collection_type' => FormCollectionInterface::COLLECTION_TYPE_MANY),
                array(
                    'cc' => array(
                        'card_brand' => 'getCardBrand',
                        'card_holder_name' => 'getCardHolderName',
                        'card_number' => 'getCardNumber',
                        'expiration_date' => 'getExpirationDate'
                    ),
                    'dd' => array(
                        'account_holder_name' => 'getAccountHolderName',
                        'iban' => 'getIban',
                        'bic' => 'getBic',
                        'bank' => 'getBank'
                    )
                ),
                array(
                    'ncform_option' => array('cc', 'dd'),
                    'cc' => array(
                        'card_brand' => 'VISA',
                        'card_holder_name' => 'Max Mustermann',
                        'card_number' => '4111111111111111',
                        'expiration_date' => '12-2015'
                    ),
                ),
                false,
                false,
                array('dd' => array('account_holder_name', 'iban', 'bic', 'bank')),
                '#10 invalid dataset form collection of type MANY with option cc and dd and missing form data for dd'
            ),
        );
    }

    /**
     * @dataProvider entityCollectionProvider
     * @param    array      $entities           array of entities (plain old php objects)
     * @param	 array		$config			    form configuration
     * @param    array      $methods            array of getters for the entity
     * @param    array      $dataMap            form data
     * @param    bool       $expectedIsBound    expected form bind result
     * @param    bool       $expectedIsValid    expected form validation result
     * @param    array      $expectedErrors     expected form validation errors
     * @param    string     $message            dataset description
     */
    public function testFormCollectionBinds($entities, $config, $methods, $dataMap, $expectedIsBound, $expectedIsValid, $expectedErrors, $message)
    {
        $formManager = new FormManager();
        $formManager->setSynchronizerToken($this->synchronizerToken);
        $form = $formManager->createFormCollection($entities, 'test_payment_forms', $config);
        $form->bind();

        $this->assertInstanceOf(FormCollectionInterface::class, $form);

        $payload = array();
        $payload['test_payment_forms'] = $dataMap;
        $payload['_csrf_token'] = $form->getSynchronizerToken();

        $form = $formManager->createFormCollection($entities, 'test_payment_forms', $config);
        $form->bind($payload);

        $collectionType = $form->getConfiguration()->get('collection_type');
        if ($collectionType == FormCollectionInterface::COLLECTION_TYPE_ONE) {
            $expectedEntites = array($entities[$dataMap['ncform_option']]);
        } elseif ($collectionType == FormCollectionInterface::COLLECTION_TYPE_MANY) {
            $expectedEntites = array();
            foreach ($dataMap['ncform_option'] as $entityName) {
                $expectedEntites[] = $entities[$entityName];
            }
        } else {
            $expectedEntites = array_values($entities);
        }


        $this->assertEquals($expectedIsBound, $form->isBound(), 'unexpected form binding result');
        $this->assertEquals($expectedIsValid, $form->isValid(), 'unexpected form validation result');
        if ($expectedIsBound) {
            $this->assertEquals($expectedEntites, $form->getBoundEntities(), 'bound entities are not equal');
        }
        $this->assertEquals('TOKEN', $form->getSynchronizerToken(), 'unexpected csrf token');

        foreach ($dataMap as $entityName => $entityPayload) {
            if ($entityName == 'ncform_option') {
                continue;
            }

            foreach ($entityPayload as $key => $value) {
                $methodName = $methods[$entityName][$key];
                if ($collectionType == FormCollectionInterface::COLLECTION_TYPE_ONE && $entityName != $dataMap['ncform_option']) {
                    $this->assertNull($entities[$entityName]->$methodName(), 'unexpected form property value of ' . $key . ', getter ' . $methodName . '()');
                } elseif ($collectionType == FormCollectionInterface::COLLECTION_TYPE_MANY) {
                    if (in_array($entityName, $dataMap['ncform_option'])) {
                        $this->assertEquals($value, $entities[$entityName]->$methodName(), 'unexpected form property value of ' . $key . ', getter ' . $methodName . '()');
                    } else {
                        $this->assertNull($entities[$entityName]->$methodName(), 'unexpected form property value of ' . $key . ', getter ' . $methodName . '()');
                    }
                } elseif ($collectionType == FormCollectionInterface::COLLECTION_TYPE_ALL) {
                    $this->assertEquals($value, $entities[$entityName]->$methodName(), 'unexpected form property value of ' . $key . ', getter ' . $methodName . '()');
                }
            }
        }

        $errors = $form->getErrors();

        $this->assertCount(count($expectedErrors), $errors, 'unexpected form error count');
        foreach ($expectedErrors as $entityName => $expectedEntityErrors) {
            $this->assertCount(count($expectedEntityErrors), $errors[$entityName], 'unexpected form error count for entity ' . $entityName);
            foreach ($expectedEntityErrors as $expectedEntityError) {
                $this->assertArrayHasKey($expectedEntityError, $errors[$entityName], 'form errors do not contain ' . $expectedEntityError);
            }
        }
    }
}
