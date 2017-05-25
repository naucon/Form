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

use Naucon\Form\Configuration;
use Naucon\Form\FormCollection;
use Naucon\Form\FormCollectionInterface;
use Naucon\Form\Validator\Validator;
use Naucon\Form\Tests\Entities\Product;
use Naucon\Form\Tests\Entities\CreditCard;
use Naucon\Form\Tests\Entities\DirectDebit;

class FormCollectionTest extends \PHPUnit_Framework_TestCase
{
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
            array(
                array(new Product()),
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
                true,
                true,
                array(),
                'valid form with one indexed entity',
            ),
            array(
                array(new Product()),
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
                        'price' => 'free'
                    )
                ),
                true,
                false,
                array(
                    array('price')
                ),
                'invalid valid form with one indexed entity',
            ),
            array(
                array('product' => new Product()),
                array(),
                array(
                    'product_id' => 'getProductId',
                    'product_number' => 'getProductNumber',
                    'product_desc' => 'getProductDesc',
                    'price' => 'getPrice'
                ),
                array(
                    'product' => array(
                        'product_number' => 'V001',
                        'product_desc' => 'Apple',
                        'price' => '9.95'
                    )
                ),
                true,
                true,
                array(),
                'valid form with one associated entity',
            ),
            array(
                array('product' => new Product()),
                array(),
                array(
                    'product_id' => 'getProductId',
                    'product_number' => 'getProductNumber',
                    'product_desc' => 'getProductDesc',
                    'price' => 'getPrice'
                ),
                array(
                    'product' => array(
                        'product_number' => 'V001',
                        'product_desc' => 'Apple',
                        'price' => 'free'
                    )
                ),
                true,
                false,
                array(
                    'product' => array('price')
                ),
                'invalid dataset form with one associated entity',
            ),
        );
    }

    /**
     * @dataProvider entitiesProvider
     * @param    array      $entities           array of entities (plain old php objects)
     * @param	 array		$config			    form configuration
     * @param    array      $methods            array of getters for the entity
     * @param    array      $dataMap            form data
     * @param    bool       $expectedIsBound    expected form bind result
     * @param    bool       $expectedIsValid    expected form validation result
     * @param    array      $expectedErrors     expected form validation errors
     */
    public function testFormBinds($entities, $config, $methods, $dataMap, $expectedIsBound, $expectedIsValid, $expectedErrors)
    {
        $configuration = new Configuration($config);
        $validator = new Validator($configuration);

        $form = new FormCollection($entities, 'testforms', $configuration);
        $form->setValidator($validator);
        $form->bind();

        $payload = array();
        $payload['_csrf_token'] = $form->getSynchronizerToken();
        $payload['testforms'] = $dataMap;

        $form = new FormCollection($entities, 'testforms', $configuration);
        $form->setValidator($validator);
        $form->bind($payload);

        $this->assertEquals($expectedIsBound, $form->isBound(), 'unexpected form binding result');
        $this->assertEquals($expectedIsValid, $form->isValid(), 'unexpected form validation result');
        if ($expectedIsBound) {
            if (count($entities) > 1) {
                $this->assertEquals($entities, $form->getBoundEntities(), 'bound entities are not equal');
            } else {
                $entity = current($entities);
                $this->assertEquals($entity, $form->getBoundEntity(), 'bound entity are not equal');
            }
        }
        $this->assertEquals('TOKEN', $form->getSynchronizerToken(), 'unexpected csrf token');

        // multiple entites
        foreach ($dataMap as $entityName => $entityPayload) {
            foreach ($entityPayload as $key => $value) {
                $methodName = $methods[$key];
                $this->assertEquals($value, $entities[$entityName]->$methodName(), 'unexpected form property value of ' . $key . ', getter ' . $methodName . '()');
            }
        }

        $errors = $form->getErrors();

        $this->assertCount(count($expectedErrors), $errors, 'unexpected form error count');
        // multiple entites
        foreach ($expectedErrors as $entityName => $expectedEntityErrors) {
            $this->assertCount(count($expectedEntityErrors), $errors[$entityName], 'unexpected form error count for entity ' . $entityName);
            foreach ($expectedEntityErrors as $expectedEntityError) {
                $this->assertArrayHasKey($expectedEntityError, $errors[$entityName], 'form errors do not contain ' . $expectedEntityError);
            }
        }
    }

    /**
     * @dataProvider entitiesProvider
     * @param    array      $entities           array of entities (plain old php objects)
     * @param	 array		$config			    form configuration
     * @param    array      $methods            array of getters for the entity
     * @param    array      $dataMap            form data
     * @param    bool       $expectedIsBound    expected form bind result
     * @param    bool       $expectedIsValid    expected form validation result
     * @param    array      $expectedErrors     expected form validation errors
     */
    public function testFormBindsWithInvalidToken($entities, $config, $methods, $dataMap, $expectedIsBound, $expectedIsValid, $expectedErrors)
    {
        $configuration = new Configuration($config);
        $validator = new Validator($configuration);

        $form = new FormCollection($entities, 'testforms', $configuration);
        $form->setValidator($validator);
        $form->bind();

        $payload = array();
        $payload['testforms'] = $dataMap;
        $payload['_csrf_token'] = 'invalid Token';

        $form = new FormCollection($entities, 'testforms', $configuration);
        $form->setValidator($validator);
        $form->bind($payload);

        $this->assertFalse($form->isBound(), 'unexpected form binding result, should be false because of invalid token');
    }

    /**
     * @dataProvider entitiesProvider
     * @param    array      $entities           array of entities (plain old php objects)
     * @param	 array		$config			    form configuration
     * @param    array      $methods            array of getters for the entity
     * @param    array      $dataMap            form data
     * @param    bool       $expectedIsBound    expected form bind result
     * @param    bool       $expectedIsValid    expected form validation result
     * @param    array      $expectedErrors     expected form validation errors
     */
    public function testFormBindsWithTokenParameter($entities, $config, $methods, $dataMap, $expectedIsBound, $expectedIsValid, $expectedErrors)
    {
        $config = array_replace($config, array('csrf_parameter' => '_bar_csrf_token'));

        $configuration = new Configuration($config);
        $validator = new Validator($configuration);

        $form = new FormCollection($entities, 'testforms', $configuration);
        $form->setValidator($validator);

        $payload = array();
        $payload['testforms'] = $dataMap;
        $payload['_bar_csrf_token'] = $form->getSynchronizerToken();

        $form->bind($payload);

        $this->assertEquals($expectedIsBound, $form->isBound(), 'unexpected form binding result');
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
                'valid dataset form collection of type ALL'
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
                'invalid dataset form collection of type ALL with missing dd form data'
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
                'valid dataset form collection of type ONE with option dd'
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
                'invalid dataset form collection of type ONE with option dd and missing from data of dd'
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
                'valid dataset form collection of type MANY with option cc and dd'
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
                'invalid dataset form collection of type MANY with option cc and dd'
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
                'invalid dataset form collection of type MANY with option cc and dd'
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
                'invalid dataset form collection of type MANY with option cc and dd and missing form data for cc and dd'
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
                'invalid dataset form collection of type MANY with option cc and dd and missing form data for dd'
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
     */
    public function testFormCollectionBinds($entities, $config, $methods, $dataMap, $expectedIsBound, $expectedIsValid, $expectedErrors)
    {
        $configuration = new Configuration($config);
        $validator = new Validator($configuration);

        $form = new FormCollection($entities, 'test_payment_forms', $configuration);
        $form->setValidator($validator);
        $form->bind();

        $payload = array();
        $payload['test_payment_forms'] = $dataMap;
        $payload['_csrf_token'] = $form->getSynchronizerToken();

        $form = new FormCollection($entities, 'test_payment_forms', $configuration);
        $form->setValidator($validator);
        $form->bind($payload);

        $collectionType = $configuration->get('collection_type');
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
                } else {
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

    /**
     * @dataProvider entityCollectionProvider
     * @param    array      $entities               array of entities (plain old php objects)
     * @param	 array		$config			        form configuration
     * @param    array      $methods                array of getters for the entity
     * @param    array      $dataMap                form data
     * @param    bool       $expectedIsBound        expected form bind result
     * @param    bool       $expectedIsValid        expected form validation result
     * @param    array      $expectedErrors         expected form validation errors
     */
    public function testFormCollectionBindsWithInvalidToken($entities, $config, $methods, $dataMap, $expectedIsBound, $expectedIsValid, $expectedErrors)
    {
        $configuration = new Configuration($config);

        $form = new FormCollection($entities, 'test_payment_forms', $configuration);
        $form->bind();

        $payload = array();
        $payload['test_payment_forms'] = $dataMap;
        $payload['_csrf_token'] = 'invalid token';

        $form = new FormCollection($entities, 'test_payment_forms', $configuration);
        $form->bind($payload);

        $this->assertFalse($form->isBound(), 'unexpected form binding result, should be false because of invalid token');
    }
}