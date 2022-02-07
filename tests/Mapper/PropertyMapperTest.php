<?php
/*
 * Copyright 2008 Sven Sanzenbacher
 *
 * This file is part of the naucon package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Naucon\Form\Tests\Mapper;

use Naucon\Form\Mapper\PropertyMapper;
use Naucon\Form\Tests\Entities\User;
use Naucon\Form\Tests\Entities\Product;
use Naucon\Form\Tests\Entities\Address;
use Naucon\Form\Tests\Entities\CreditCard;
use Naucon\Form\Tests\Entities\DirectDebit;
use PHPUnit\Framework\TestCase;

class PropertyMapperTest extends TestCase
{
    public function entityProvider()
    {
        return [
            [
                new User(),
                [
                    'username' => 'getUsername',
                    'firstname' => 'getFirstname',
                    'lastname' => 'getLastname',
                    'email' => 'getEmail',
                    'age' => 'getAge'
                ],
                [
                    'username' => 'setUsername',
                    'firstname' => 'setFirstname',
                    'lastname' => 'setLastname',
                    'email' => 'setEmail',
                    'age' => 'setAge'
                ],
                [
                    'username' => 'max.mustermann',
                    'firstname' => 'Max',
                    'lastname' => 'Mustermann',
                    'email' => 'max.mustermann@yourdomain.com',
                    'age' => '18'
                ],
                'valid dataset of user data'
            ],
            [
                new User(),
                [
                    'username' => 'getUsername',
                    'firstname' => 'getFirstname',
                    'lastname' => 'getLastname',
                    'email' => 'getEmail',
                    'age' => 'getAge'
                ],
                [
                    'username' => 'setUsername',
                    'firstname' => 'setFirstname',
                    'lastname' => 'setLastname',
                    'email' => 'setEmail',
                    'age' => 'setAge'
                ],
                [
                    'username' => 'max.mustermann',
                    'firstname' => 'Max',
                    'lastname' => 'Mustermann',
                    'email' => 'max.mustermann@yourdomain.com',
                    'age' => '18',
                    'unkown' => 'any'
                ],
                'valid dataset of user data with more form data as in entity'
            ],
            [
                new Address(),
                [
                    'street_name' => 'getStreetName',
                    'street_number' => 'getStreetNumber',
                    'postal_code' => 'getPostalCode',
                    'town' => 'getTown',
                    'country' => 'getCountry'
                ],
                [
                    'street_name' => 'setStreetName',
                    'street_number' => 'setStreetNumber',
                    'postal_code' => 'setPostalCode',
                    'town' => 'setTown',
                    'country' => 'setCountry'
                ],
                [
                    'street_name' => 'Any-Street',
                    'street_number' => '1',
                    'postal_code' => '12345',
                    'town' => 'Anywhere',
                    'country' => 'Anyland'
                ],
                'valid dataset of address data'
            ],
            [
                new Product(),
                [
                    'product_id' => 'getProductId',
                    'product_number' => 'getProductNumber',
                    'product_desc' => 'getProductDesc',
                    'price' => 'getPrice'
                ],
                [
                    'product_id' => 'setProductId',
                    'product_number' => 'setProductNumber',
                    'product_desc' => 'setProductDesc',
                    'price' => 'setPrice'
                ],
                [
                    'product_id' => null,
                    'product_number' => 'V001',
                    'product_desc' => 'Apple',
                    'price' => '9.91'
                ],
                'valid dataset of product data'
            ],
            [
                new CreditCard(),
                [
                    'card_brand' => 'getCardBrand',
                    'card_holder_name' => 'getCardHolderName',
                    'card_number' => 'getCardNumber',
                    'expiration_date' => 'getExpirationDate'
                ],
                [
                    'card_brand' => 'setCardBrand',
                    'card_holder_name' => 'setCardHolderName',
                    'card_number' => 'setCardNumber',
                    'expiration_date' => 'setExpirationDate'
                ],
                [
                    'card_brand' => 'VISA',
                    'card_holder_name' => 'Max Mustermann',
                    'card_number' => '4111111111111111',
                    'expiration_date' => '12-2015'
                ],
                'valid dataset of credit card data'
            ],
            [
                new DirectDebit(),
                [
                    'account_holder_name' => 'getAccountHolderName',
                    'iban' => 'getIban',
                    'bic' => 'getBic',
                    'bank' => 'getBank'
                ],
                [
                    'account_holder_name' => 'setAccountHolderName',
                    'iban' => 'setIban',
                    'bic' => 'setBic',
                    'bank' => 'setBank'
                ],
                [
                    'account_holder_name' => 'Max Mustermann',
                    'iban' => 'DE00210501700012345678',
                    'bic' => 'MARKDEFF',
                    'bank' => 'Bundesbank'
                ],
                'valid dataset of debit direct data'
            ]
        ];
    }

    /**
     * @dataProvider entityProvider
     * @param	object		$entity				entity a plain old php object
     * @param	array		$getter			    array of getters for the entity
     * @param	array		$setter			    array of setters for the entity
     * @param	array		$dataMap			form data
     */
    public function testMapFormToData($entity, $getter, $setter, $dataMap)
    {
        $propertyMapper = new PropertyMapper();
        $propertyMapper->mapFormToData($entity, $dataMap);

        foreach ($dataMap as $key => $value) {
            if (array_key_exists($key, $getter)) {
                $methodName = $getter[$key];
                $actualValue = $entity->$methodName();
                $this->assertEquals($value, $actualValue, 'unexpected form property value of ' . $key . ', getter ' . $methodName . '()');
            }
        }
    }

    public function testMapFormToDataOfProtectedProperty()
    {
        $entity = new User();

        $dataMap = [
            'username' => 'max.mustermann',
            'firstname' => 'Max',
            'lastname' => 'Mustermann',
            'email' => 'max.mustermann@yourdomain.com',
            'age' => '18',
            'secret' => 'hack'
        ];

        $propertyMapper = new PropertyMapper();
        $propertyMapper->mapFormToData($entity, $dataMap);

        $this->assertEquals('secrethash', $actualValue = $entity->getSecret(), 'protected form property value of secure was overwritten');
    }

    /**
     * @dataProvider entityProvider
     * @param	object		$entity				entity a plain old php object
     * @param	array		$getter			    array of getters for the entity
     * @param	array		$setter			    array of setters for the entity
     * @param	array		$dataMap			form data
     */
    public function testMapDataToForm($entity, $getter, $setter, $dataMap)
    {
        foreach ($dataMap as $key => $value) {
            if (array_key_exists($key, $setter)) {
                $methodName = $setter[$key];
                $entity->$methodName($value);
            }
        }

        $propertyMapper = new PropertyMapper();

        foreach ($dataMap as $key => $value) {
            if (array_key_exists($key, $setter)) {
                $this->assertEquals($value, $propertyMapper->mapDataToForm($entity, $key), 'unexpected form data value of ' . $key);
            } else {
                $this->assertNull($propertyMapper->mapDataToForm($entity, $key), 'unexpected form data value of ' . $key);
            }
        }
    }
}
