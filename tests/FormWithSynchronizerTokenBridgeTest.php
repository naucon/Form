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
use Naucon\Form\FormCollection;
use Naucon\Form\Security\SynchronizerTokenBridge;
use Naucon\Form\Tests\Entities\User;
use Naucon\Form\Tests\Entities\Product;
use Naucon\Form\Tests\Entities\Address;
use Symfony\Component\Security\Csrf\CsrfTokenManager;

class FormWithSynchronizerTokenBridgeTest extends \PHPUnit_Framework_TestCase
{
	protected function setUp()
	{
		$this->generator = $this->getMock('Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface');
		$this->storage = $this->getMock('Symfony\Component\Security\Csrf\TokenStorage\TokenStorageInterface');
		$this->manager = new CsrfTokenManager($this->generator, $this->storage);
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
					'email' => 'max.mustermann@yourdomain.com',
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
	 * @param	object		$entity				entity a plain old php object
	 * @param	array		$config			    form configuration
	 * @param	array		$methods			array of getters for the entity
	 * @param	array		$dataMap			form data
	 * @param	bool		$expectedIsBound	expected form bind result
	 * @param	bool		$expectedIsValid	expected form validation result
	 * @param	array		$expectedErrors		expected form validation errors
	 */
	public function testFormBind($entity, $config, $methods, $dataMap, $expectedIsBound, $expectedIsValid, $expectedErrors)
	{
		foreach ($methods as $method) {
			$this->assertEquals('', $entity->$method());
		}

		$synchronizerToken = new SynchronizerTokenBridge($this->manager);

		$this->storage->expects($this->any())
			->method('hasToken')
			->with('testform')
			->will($this->returnValue(true));

		$this->storage->expects($this->any())
			->method('getToken')
			->with('testform')
			->will($this->returnValue('TOKEN'));

		$configuration = new Configuration($config);

        $form = new Form($entity, 'testform', $configuration);
		$form->setSynchronizerToken($synchronizerToken);
        $form->bind();

	    $payload = array();
		$payload['_csrf_token'] = $form->getSynchronizerToken();
		if (count($dataMap)) {
		    $payload['testform'] = $dataMap;
		}

        $form = new Form($entity, 'testform', $configuration);
		$form->setSynchronizerToken($synchronizerToken);
        $form->bind($payload);

		$this->assertEquals($expectedIsBound, $form->isBound(), 'unexpected form binding result');
		if ($expectedIsBound) {
			$this->assertEquals($entity, $form->getBoundEntity(), 'bound entities are not equal');
		}
		$this->assertEquals('TOKEN', $form->getSynchronizerToken(), 'unexpected csrf token');
	}

	/**
	 * @dataProvider entityProvider
	 * @param	object		$entity				entity a plain old php object
	 * @param	 array		$config			    form configuration
	 * @param	array		$methods			array of getters for the entity
	 * @param	array		$dataMap			form data
	 * @param	bool		$expectedIsBound	expected form bind result
	 * @param	bool		$expectedIsValid	expected form validation result
	 * @param	array		$expectedErrors		expected form validation errors
	 */
	public function testFormBindWithInvalidToken($entity, $config, $methods, $dataMap, $expectedIsBound, $expectedIsValid, $expectedErrors)
	{
		foreach ($methods as $method) {
			$this->assertEquals('', $entity->$method());
		}

		$synchronizerToken = new SynchronizerTokenBridge($this->manager);

		$this->storage->expects($this->any())
			->method('hasToken')
			->with('testform')
			->will($this->returnValue(true));

		$this->storage->expects($this->any())
			->method('getToken')
			->with('testform')
			->will($this->returnValue('TOKEN'));

		$configuration = new Configuration($config);

		$form = new Form($entity, 'testform', $configuration);
		$form->setSynchronizerToken($synchronizerToken);
		$form->bind();

		$payload = array();
		$payload['testform'] = $dataMap;
		$payload['_csrf_token'] = 'invalid token';

		$form = new Form($entity, 'testform', $configuration);
		$form->setSynchronizerToken($synchronizerToken);
		$form->bind($payload);

		$this->assertFalse($form->isBound(), 'unexpected form binding result, should be false because of invalid token');
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
	 * @param	array		$entities			array of entities (plain old php objects)
	 * @param	array		$config			    form configuration
	 * @param	array		$methods			array of getters for the entity
	 * @param	array		$dataMap			form data
	 * @param	bool		$expectedIsBound	expected form bind result
	 * @param	bool		$expectedIsValid	expected form validation result
	 * @param	array		$expectedErrors		expected form validation errors
	 */
	public function testFormCollectionBinds($entities, $config, $methods, $dataMap, $expectedIsBound, $expectedIsValid, $expectedErrors)
	{
		$synchronizerToken = new SynchronizerTokenBridge($this->manager);

		$this->storage->expects($this->any())
			->method('hasToken')
			->with('testforms')
			->will($this->returnValue(true));

		$this->storage->expects($this->any())
			->method('getToken')
			->with('testforms')
			->will($this->returnValue('TOKEN'));

		$configuration = new Configuration($config);

		$form = new FormCollection($entities, 'testforms', $configuration);
		$form->setSynchronizerToken($synchronizerToken);
		$form->bind();

		$payload = array();
		$payload['testforms'] = $dataMap;
		$payload['_csrf_token'] = $form->getSynchronizerToken();

		$form = new FormCollection($entities, 'testforms', $configuration);
		$form->setSynchronizerToken($synchronizerToken);
		$form->bind($payload);

		$this->assertEquals($expectedIsBound, $form->isBound(), 'unexpected form binding result');
		if ($expectedIsBound) {
			$this->assertEquals($entities, $form->getBoundEntities(), 'bound entities are not equal');
		}
		$this->assertEquals('TOKEN', $form->getSynchronizerToken(), 'unexpected csrf token');
	}

	/**
	 * @dataProvider entitiesProvider
	 * @param	array		$entities			array of entities (plain old php objects)
	 * @param	array		$config			    form configuration
	 * @param	array		$methods			array of getters for the entity
	 * @param	array		$dataMap			form data
	 * @param	bool		$expectedIsBound	expected form bind result
	 * @param	bool		$expectedIsValid	expected form validation result
	 * @param	array		$expectedErrors		expected form validation errors
	 */
	public function testFormCollectionBindsWithInvalidToken($entities, $config, $methods, $dataMap, $expectedIsBound, $expectedIsValid, $expectedErrors)
	{
		$synchronizerToken = new SynchronizerTokenBridge($this->manager);

		$this->storage->expects($this->any())
			->method('hasToken')
			->with('testforms')
			->will($this->returnValue(true));

		$this->storage->expects($this->any())
			->method('getToken')
			->with('testforms')
			->will($this->returnValue('TOKEN'));

		$configuration = new Configuration($config);

		$form = new FormCollection($entities, 'testforms', $configuration);
		$form->setSynchronizerToken($synchronizerToken);
		$form->bind();

		$payload = array();
		$payload['testforms'] = $dataMap;
		$payload['_csrf_token'] = 'invalid token';

		$form = new FormCollection($entities, 'testforms', $configuration);
		$form->setSynchronizerToken($synchronizerToken);
		$form->bind($payload);

		$this->assertFalse($form->isBound(), 'unexpected form binding result, should be false because of invalid token');
	}
}