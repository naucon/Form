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
use Naucon\Form\FormHelper;
use Naucon\Form\Helper\FormHelperFieldHidden;
use Naucon\Form\Helper\FormHelperFieldText;
use Naucon\Form\Helper\FormHelperFieldTextarea;
use Naucon\Form\Helper\FormHelperFieldValue;
use Naucon\Form\Mapper\EntityContainerInterface;
use Naucon\Form\Helper\FormHelperChoiceCheckbox;
use Naucon\Form\Helper\FormHelperChoiceRadio;
use Naucon\Form\Helper\FormHelperChoiceSelect;
use Naucon\Form\Mapper\Property;
use Naucon\Form\Tests\Entities\User;
use Naucon\Form\Tests\Entities\Product;
use Naucon\Form\Validator\Validator;

class FormHelperTest extends \PHPUnit_Framework_TestCase
{
    public function formWithConfigProvider()
    {
        $user = new User();
        $user->setUsername('max.mustermann');
        $user->setFirstname('Max');
        $user->setLastname('Mustermann');
        $user->setEmail('max.mustermann@yourdomain.com');
        $user->setAge(18);

        $product1 = new Product();
        $product1->setProductId(1);
        $product1->setProductNumber('V001');
        $product1->setProductDesc('Apple');
        $product1->setPrice(9.91);

        $product2 = new Product();
        $product2->setProductId(2);
        $product2->setProductNumber('V002');
        $product2->setProductDesc('Banana');
        $product2->setPrice(9.92);

        $product3 = new Product();
        $product3->setProductId(3);
        $product3->setProductNumber('V003');
        $product3->setProductDesc('Orange');
        $product3->setPrice(9.95);

        return array(
            array(
                $user,
                array(),
                null,
                '<form method="post" id="testform">' . PHP_EOL
                . '<input type="hidden" name="_csrf_token" value="TOKEN" />' . PHP_EOL,
                '</form>' . PHP_EOL,
                'valid dataset of user data without bind data'
            ),
            array(
                $user,
                array(),
                array(
                    'username' => 'max.mustermann',
                    'firstname' => 'Max',
                    'lastname' => 'Mustermann',
                    'email' => 'max.mustermann@yourdomain.com',
                    'age' => '18'
                ),
                '<form method="post" id="testform">' . PHP_EOL
                . '<input type="hidden" name="_csrf_token" value="TOKEN" />' . PHP_EOL,
                '</form>' . PHP_EOL,
                'valid dataset of user data with bind data'
            ),
            array(
                $user,
                array(),
                array(
                    'username' => 'max',
                    'firstname' => 'Max',
                    'lastname' => '',
                    'email' => 'max.mustermann@yourdomain.com',
                    'age' => ''
                ),
                '<form method="post" id="testform">' . PHP_EOL
                . '<input type="hidden" name="_csrf_token" value="TOKEN" />' . PHP_EOL,
                '</form>' . PHP_EOL,
                'invalid dataset of user data with missing data'
            ),
            array(
                $user,
                array('csrf_parameter' => '_foo_csrf_token'),
                null,
                '<form method="post" id="testform">' . PHP_EOL
                . '<input type="hidden" name="_foo_csrf_token" value="TOKEN" />' . PHP_EOL,
                '</form>' . PHP_EOL,
                'valid dataset of user data with custom csrf token parameter and without bind data'
            ),
            array(
                $user,
                array('csrf_parameter' => '_foo_csrf_token'),
                array(
                    'username' => 'max.mustermann',
                    'firstname' => 'Max',
                    'lastname' => 'Mustermann',
                    'email' => 'max.mustermann@yourdomain.com',
                    'age' => '18'
                ),
                '<form method="post" id="testform">' . PHP_EOL
                . '<input type="hidden" name="_foo_csrf_token" value="TOKEN" />' . PHP_EOL,
                '</form>' . PHP_EOL,
                'valid dataset of user data with custom csrf token parameter and with bind data'
            ),
            array(
                $user,
                array('csrf_parameter' => '_foo_csrf_token'),
                array(
                    'username' => 'max',
                    'firstname' => 'Max',
                    'lastname' => '',
                    'email' => 'max.mustermann@yourdomain.com',
                    'age' => ''
                ),
                '<form method="post" id="testform">' . PHP_EOL
                . '<input type="hidden" name="_foo_csrf_token" value="TOKEN" />' . PHP_EOL,
                '</form>' . PHP_EOL,
                'invalid dataset of user data with custom csrf token parameter and missing data'
            )
        );
    }

    /**
     * @dataProvider formWithConfigProvider
     * @param	object		$entity				entity a plain old php object
     * @param	array		$config			    form configuration
     * @param	array		$dataMap			form data
     * @param	bool		$expectedFormStart	expected form start tag html
     * @param	bool		$expectedFormEnd	expected form end tag html
     */
    public function testFormHelperWithConfig($entity, $config, $dataMap, $expectedFormStart, $expectedFormEnd)
    {
        $configuration = new Configuration($config);
        $form = new Form($entity, 'testform', $configuration);

        if (!is_null($dataMap)) {
            $payload = array();
            $payload['testform'] = $dataMap;
            if (isset($config['csrf_parameter'])) {
                $payload[$config['csrf_parameter']] = 'TOKEN';
            } else {
                $payload['_csrf_token'] = 'TOKEN';
            }
            $form->bind($payload);
            $form->isValid();
        } else {
            $form->bind();
        }
        $formHelper = new FormHelper($form);

        $this->assertEquals($expectedFormStart, $formHelper->formStart());
        $this->assertEquals($expectedFormEnd, $formHelper->formEnd());
    }

    public function formFieldProvider()
    {
        $user = new User();
        $user->setUsername('max.mustermann');
        $user->setFirstname('Max');
        $user->setLastname('Mustermann');
        $user->setEmail('max.mustermann@yourdomain.com');
        $user->setAge(18);

        return array(
            array(
                $user,
                null,
                '<form method="post" id="testform">' . PHP_EOL
                . '<input type="hidden" name="_csrf_token" value="TOKEN" />' . PHP_EOL,
                '</form>' . PHP_EOL,
                array(
                    'text' => array(
                        0 => array(
                            'username' => '<input type="text" name="testform[username]" value="max.mustermann" id="testform_username" />' . PHP_EOL,
                            'firstname' => '<input type="text" name="testform[firstname]" value="Max" id="testform_firstname" />' . PHP_EOL,
                            'lastname' => '<input type="text" name="testform[lastname]" value="Mustermann" id="testform_lastname" />' . PHP_EOL,
                            'email' => '<input type="text" name="testform[email]" value="max.mustermann@yourdomain.com" id="testform_email" />' . PHP_EOL,
                            'age' => '<input type="text" name="testform[age]" value="18" id="testform_age" />' . PHP_EOL
                        )
                    ),
                    'label' => array(
                        0 => array(
                            'username' => '<label for="testform_username">testform.username</label>' . PHP_EOL,
                            'firstname' => '<label for="testform_firstname">testform.firstname</label>' . PHP_EOL,
                            'lastname' => '<label for="testform_lastname">testform.lastname</label>' . PHP_EOL,
                            'email' => '<label for="testform_email">testform.email</label>' . PHP_EOL,
                            'age' => '<label for="testform_age">testform.age</label>' . PHP_EOL
                        )
                    ),
                    'error' => array(
                        0 => array(
                            'username' => null,
                            'firstname' => null,
                            'lastname' => null,
                            'email' => null,
                            'age' => null
                        )
                    )
                ),
                'valid dataset of user data without bind data'
            ),
            array(
                $user,
                array(
                    'username' => 'max.mustermann',
                    'firstname' => 'Max',
                    'lastname' => 'Mustermann',
                    'email' => 'max.mustermann@yourdomain.com',
                    'age' => '18'
                ),
                '<form method="post" id="testform">' . PHP_EOL
                . '<input type="hidden" name="_csrf_token" value="TOKEN" />' . PHP_EOL,
                '</form>' . PHP_EOL,
                array(
                    'text' => array(
                        0 => array(
                            'username' => '<input type="text" name="testform[username]" value="max.mustermann" id="testform_username" />' . PHP_EOL,
                            'firstname' => '<input type="text" name="testform[firstname]" value="Max" id="testform_firstname" />' . PHP_EOL,
                            'lastname' => '<input type="text" name="testform[lastname]" value="Mustermann" id="testform_lastname" />' . PHP_EOL,
                            'email' => '<input type="text" name="testform[email]" value="max.mustermann@yourdomain.com" id="testform_email" />' . PHP_EOL,
                            'age' => '<input type="text" name="testform[age]" value="18" id="testform_age" />' . PHP_EOL
                        )
                    ),
                    'label' => array(
                        0 => array(
                            'username' => '<label for="testform_username">testform.username</label>' . PHP_EOL,
                            'firstname' => '<label for="testform_firstname">testform.firstname</label>' . PHP_EOL,
                            'lastname' => '<label for="testform_lastname">testform.lastname</label>' . PHP_EOL,
                            'email' => '<label for="testform_email">testform.email</label>' . PHP_EOL,
                            'age' => '<label for="testform_age">testform.age</label>' . PHP_EOL
                        )
                    ),
                    'error' => array(
                        0 => array(
                            'username' => null,
                            'firstname' => null,
                            'lastname' => null,
                            'email' => null,
                            'age' => null
                        )
                    )
                ),
                'valid dataset of user data with bind data'
            ),
            array(
                $user,
                array(
                    'username' => 'max',
                    'firstname' => 'Max',
                    'lastname' => '',
                    'email' => 'max.mustermann@yourdomain.com',
                    'age' => ''
                ),
                '<form method="post" id="testform">' . PHP_EOL
                . '<input type="hidden" name="_csrf_token" value="TOKEN" />' . PHP_EOL,
                '</form>' . PHP_EOL,
                array(
                    'text' => array(
                        0 => array(
                            'username' => '<input type="text" name="testform[username]" value="max" id="testform_username" class="nc_form_error" />' . PHP_EOL,
                            'firstname' => '<input type="text" name="testform[firstname]" value="Max" id="testform_firstname" />' . PHP_EOL,
                            'lastname' => '<input type="text" name="testform[lastname]" value="" id="testform_lastname" />' . PHP_EOL,
                            'email' => '<input type="text" name="testform[email]" value="max.mustermann@yourdomain.com" id="testform_email" />' . PHP_EOL,
                            'age' => '<input type="text" name="testform[age]" value="" id="testform_age" class="nc_form_error" />' . PHP_EOL
                        )
                    ),
                    'label' => array(
                        0 => array(
                            'username' => '<label for="testform_username">testform.username</label>' . PHP_EOL,
                            'firstname' => '<label for="testform_firstname">testform.firstname</label>' . PHP_EOL,
                            'lastname' => '<label for="testform_lastname">testform.lastname</label>' . PHP_EOL,
                            'email' => '<label for="testform_email">testform.email</label>' . PHP_EOL,
                            'age' => '<label for="testform_age">testform.age</label>' . PHP_EOL
                        )
                    ),
                    'error' => array(
                        0 => array(
                            'username' => '<span class="ncFormError">This value is too short. It should have 5 characters or more.</span>' . PHP_EOL,
                            'firstname' => null,
                            'lastname' => null,
                            'email' => null,
                            'age' => '<span class="ncFormError">This value should be of type numeric.</span>' . PHP_EOL
                        )
                    )
                ),
                'invalid dataset of user data with missing data'
            )
        );
    }

    /**
     * @dataProvider formFieldProvider
     * @param	object		$entity				    entity a plain old php object
     * @param	array		$dataMap			    form data
     * @param	string		$expectedFormStart	    expected form start tag html
     * @param	string		$expectedFormEnd	    expected form end tag html
     * @param	array		$expectedFormHelpers	array of expected form helper html
     */
    public function testFieldForm($entity, $dataMap, $expectedFormStart, $expectedFormEnd, $expectedFormHelpers)
    {
        $configuration = new Configuration();
        $validator = new Validator($configuration);

        $form = new Form($entity, 'testform', $configuration);
        $form->setValidator($validator);
        if (!is_null($dataMap)) {
            $payload = array();
            $payload['testform'] = $dataMap;
            $payload['_csrf_token'] = 'TOKEN';
            $form->bind($payload);
            $form->isValid();
        } else {
            $form->bind();
        }
        $formHelper = new FormHelper($form);

        $this->assertEquals($expectedFormStart, $formHelper->formStart());
        $this->assertEquals($expectedFormEnd, $formHelper->formEnd());

        $entityContainers = $form->getEntityContainerIterator();

        foreach ($expectedFormHelpers as $helperName => $expectedFormHelper) {
            $this->assertCount(count($expectedFormHelper), $entityContainers);
            foreach ($entityContainers as $entityName => $entityContainer) {
                $propertyCounter = 0;
                foreach ($expectedFormHelper[$entityName] as $property => $expectedFormField) {
                    $propertyCounter++;
                    $this->assertEquals($expectedFormField, $formHelper->formField($helperName, $property));
                }
                $this->assertEquals(count($expectedFormHelper[$entityName]), $propertyCounter, 'unexpected form property count');
            }
        }
    }

    /**
     * @dataProvider formFieldProvider
     * @param	object		$entity				    entity a plain old php object
     * @param	array		$dataMap			    form data
     * @param	string		$expectedFormStart	    expected form start tag html
     * @param	string		$expectedFormEnd	    expected form end tag html
     * @param	array		$expectedFormHelpers	array of expected form helper html
     */
    public function testFormIterator($entity, $dataMap, $expectedFormStart, $expectedFormEnd, $expectedFormHelpers)
    {
        $configuration = new Configuration();
        $validator = new Validator($configuration);

        $form = new Form($entity, 'testform', $configuration);
        $form->setValidator($validator);
        if (!is_null($dataMap)) {
            $payload = array();
            $payload['testform'] = $dataMap;
            $payload['_csrf_token'] = 'TOKEN';
            $form->bind($payload);
            $form->isValid();
        } else {
            $form->bind();
        }
        $formHelper = new FormHelper($form);

        $this->assertEquals($expectedFormStart, $formHelper->formStart());
        $this->assertEquals($expectedFormEnd, $formHelper->formEnd());

        foreach ($expectedFormHelpers as $helperName => $expectedFormHelper) {
            $this->assertCount(count($expectedFormHelper), $form);
            foreach ($form as $entityName => $entityContainer) {
                $propertyCounter = 0;
                foreach ($expectedFormHelper[$entityName] as $property => $expectedFormField) {
                    $propertyCounter++;
                    $this->assertEquals($expectedFormField, $formHelper->formField($helperName, $property));
                }
                $this->assertEquals(count($expectedFormHelper[$entityName]), $propertyCounter, 'unexpected form property count');
            }
        }
    }

    public function formCollectionWithConfigProvider()
    {
        $product1 = new Product();
        $product1->setProductId(1);
        $product1->setProductNumber('V001');
        $product1->setProductDesc('Apple');
        $product1->setPrice(9.91);

        $product2 = new Product();
        $product2->setProductId(2);
        $product2->setProductNumber('V002');
        $product2->setProductDesc('Banana');
        $product2->setPrice(9.92);

        $product3 = new Product();
        $product3->setProductId(3);
        $product3->setProductNumber('V003');
        $product3->setProductDesc('Orange');
        $product3->setPrice(9.95);

        return array(
            array(
                array($product1, $product2, $product3),
                array(),
                null,
                '<form method="post" id="testform">' . PHP_EOL
                . '<input type="hidden" name="_csrf_token" value="TOKEN" />' . PHP_EOL,
                '</form>' . PHP_EOL,
                'valid dataset of product data without bind data'
            ),
            array(
                array($product1, $product2, $product3),
                array(),
                array(
                    0 => array(
                        'product_number' => 'V001',
                        'product_desc' => 'Apple',
                        'price' => '9.91'
                    ),
                    1 => array(
                        'product_number' => 'V002',
                        'product_desc' => 'Banana',
                        'price' => '9.92'
                    ),
                    2 => array(
                        'product_number' => 'V003',
                        'product_desc' => 'Orange',
                        'price' => '9.93'
                    )
                ),
                '<form method="post" id="testform">' . PHP_EOL
                . '<input type="hidden" name="_csrf_token" value="TOKEN" />' . PHP_EOL,
                '</form>' . PHP_EOL,
                'valid dataset of product data with bind data'
            ),
            array(
                array($product1, $product2, $product3),
                array(),
                array(
                    0 => array(
                        'product_number' => '',
                        'product_desc' => 'Apple',
                        'price' => '9.91'
                    ),
                    1 => array(
                        'product_number' => 'V002',
                        'product_desc' => 'Banana',
                        'price' => ''
                    ),
                    2 => array(
                        'product_number' => 'V003',
                        'product_desc' => 'Orange',
                        'price' => '9.93'
                    )
                ),
                '<form method="post" id="testform">' . PHP_EOL
                . '<input type="hidden" name="_csrf_token" value="TOKEN" />' . PHP_EOL,
                '</form>' . PHP_EOL,
                'invalid dataset of product data with missing data'
            ),
            array(
                array($product1, $product2, $product3),
                array('csrf_parameter' => '_foo_csrf_token'),
                null,
                '<form method="post" id="testform">' . PHP_EOL
                . '<input type="hidden" name="_foo_csrf_token" value="TOKEN" />' . PHP_EOL,
                '</form>' . PHP_EOL,
                'valid dataset of product data with custom csrf token parameter and without bind data'
            ),
            array(
                array($product1, $product2, $product3),
                array('csrf_parameter' => '_foo_csrf_token'),
                array(
                    0 => array(
                        'product_number' => 'V001',
                        'product_desc' => 'Apple',
                        'price' => '9.91'
                    ),
                    1 => array(
                        'product_number' => 'V002',
                        'product_desc' => 'Banana',
                        'price' => '9.92'
                    ),
                    2 => array(
                        'product_number' => 'V003',
                        'product_desc' => 'Orange',
                        'price' => '9.93'
                    )
                ),
                '<form method="post" id="testform">' . PHP_EOL
                . '<input type="hidden" name="_foo_csrf_token" value="TOKEN" />' . PHP_EOL,
                '</form>' . PHP_EOL,
                'valid dataset of product data with custom csrf token parameter and with bind data'
            ),
            array(
                array($product1, $product2, $product3),
                array('csrf_parameter' => '_foo_csrf_token'),
                array(
                    0 => array(
                        'product_number' => '',
                        'product_desc' => 'Apple',
                        'price' => '9.91'
                    ),
                    1 => array(
                        'product_number' => 'V002',
                        'product_desc' => 'Banana',
                        'price' => ''
                    ),
                    2 => array(
                        'product_number' => 'V003',
                        'product_desc' => 'Orange',
                        'price' => '9.93'
                    )
                ),
                '<form method="post" id="testform">' . PHP_EOL
                . '<input type="hidden" name="_foo_csrf_token" value="TOKEN" />' . PHP_EOL,
                '</form>' . PHP_EOL,
                'invalid dataset of product data with custom csrf token parameter and missing data'
            ),
            array(
                array($product1),
                array(),
                array(
                    0 => array(
                        'product_number' => '',
                        'product_desc' => 'Apple',
                        'price' => '9.91'
                    )
                ),
                '<form method="post" id="testform">' . PHP_EOL
                . '<input type="hidden" name="_csrf_token" value="TOKEN" />' . PHP_EOL,
                '</form>' . PHP_EOL,
                'one valid dataset of product data with custom csrf token parameter'
            )
        );
    }

    /**
     * @dataProvider formCollectionWithConfigProvider
     * @param	array		$entities			entities of plain old php object
     * @param	array		$config			    form configuration
     * @param	array		$dataMap			form data
     * @param	bool		$expectedFormStart	expected form start tag html
     * @param	bool		$expectedFormEnd	expected form end tag html
     */
    public function testFormCollectionHelperWithConfig($entities, $config, $dataMap, $expectedFormStart, $expectedFormEnd)
    {
        $configuration = new Configuration($config);
        $form = new FormCollection($entities, 'testform', $configuration);

        if (!is_null($dataMap)) {
            $payload = array();
            $payload['testform'] = $dataMap;
            if (isset($config['csrf_parameter'])) {
                $payload[$config['csrf_parameter']] = 'TOKEN';
            } else {
                $payload['_csrf_token'] = 'TOKEN';
            }
            $form->bind($payload);
            $form->isValid();
        } else {
            $form->bind();
        }
        $formHelper = new FormHelper($form);

        $this->assertEquals($expectedFormStart, $formHelper->formStart());
        $this->assertEquals($expectedFormEnd, $formHelper->formEnd());
    }

    public function formCollectionFieldProvider()
    {
        $product1 = new Product();
        $product1->setProductId(1);
        $product1->setProductNumber('V001');
        $product1->setProductDesc('Apple');
        $product1->setPrice(9.91);

        $product2 = new Product();
        $product2->setProductId(2);
        $product2->setProductNumber('V002');
        $product2->setProductDesc('Banana');
        $product2->setPrice(9.92);

        $product3 = new Product();
        $product3->setProductId(3);
        $product3->setProductNumber('V003');
        $product3->setProductDesc('Orange');
        $product3->setPrice(9.95);

        return array(
            array(
                array($product1, $product2, $product3),
                null,
                '<form method="post" id="testform">' . PHP_EOL
                . '<input type="hidden" name="_csrf_token" value="TOKEN" />' . PHP_EOL,
                '</form>' . PHP_EOL,
                array(
                    'text' => array(
                        0 => array(
                            'product_number' => '<input type="text" name="testform[0][product_number]" value="V001" id="testform_0_product_number" />' . PHP_EOL,
                            'product_desc' => '<input type="text" name="testform[0][product_desc]" value="Apple" id="testform_0_product_desc" />' . PHP_EOL,
                            'price' => '<input type="text" name="testform[0][price]" value="9.91" id="testform_0_price" />' . PHP_EOL
                        ),
                        1 => array(
                            'product_number' => '<input type="text" name="testform[1][product_number]" value="V002" id="testform_1_product_number" />' . PHP_EOL,
                            'product_desc' => '<input type="text" name="testform[1][product_desc]" value="Banana" id="testform_1_product_desc" />' . PHP_EOL,
                            'price' => '<input type="text" name="testform[1][price]" value="9.92" id="testform_1_price" />' . PHP_EOL
                        ),
                        2 => array(
                            'product_number' => '<input type="text" name="testform[2][product_number]" value="V003" id="testform_2_product_number" />' . PHP_EOL,
                            'product_desc' => '<input type="text" name="testform[2][product_desc]" value="Orange" id="testform_2_product_desc" />' . PHP_EOL,
                            'price' => '<input type="text" name="testform[2][price]" value="9.95" id="testform_2_price" />' . PHP_EOL
                        )
                    ),
                    'label' => array(
                        0 => array(
                            'product_number' => '<label for="testform_0_product_number">testform.product_number</label>' . PHP_EOL,
                            'product_desc' => '<label for="testform_0_product_desc">testform.product_desc</label>' . PHP_EOL,
                            'price' => '<label for="testform_0_price">testform.price</label>' . PHP_EOL,
                        ),
                        1 => array(
                            'product_number' => '<label for="testform_1_product_number">testform.product_number</label>' . PHP_EOL,
                            'product_desc' => '<label for="testform_1_product_desc">testform.product_desc</label>' . PHP_EOL,
                            'price' => '<label for="testform_1_price">testform.price</label>' . PHP_EOL,
                        ),
                        2 => array(
                            'product_number' => '<label for="testform_2_product_number">testform.product_number</label>' . PHP_EOL,
                            'product_desc' => '<label for="testform_2_product_desc">testform.product_desc</label>' . PHP_EOL,
                            'price' => '<label for="testform_2_price">testform.price</label>' . PHP_EOL,
                        )
                    ),
                    'error' => array(
                        0 => array(
                            'product_number' => null,
                            'product_desc' => null,
                            'price' => null,
                        ),
                        1 => array(
                            'product_number' => null,
                            'product_desc' => null,
                            'price' => null,
                        ),
                        2 => array(
                            'product_number' => null,
                            'product_desc' => null,
                            'price' => null,
                        )
                    )
                ),
                'valid dataset of product data without bind data'
            ),
            array(
                array($product1, $product2, $product3),
                array(
                    0 => array(
                        'product_number' => 'V001',
                        'product_desc' => 'Apple',
                        'price' => '9.91'
                    ),
                    1 => array(
                        'product_number' => 'V002',
                        'product_desc' => 'Banana',
                        'price' => '9.92'
                    ),
                    2 => array(
                        'product_number' => 'V003',
                        'product_desc' => 'Orange',
                        'price' => '9.93'
                    )
                ),
                '<form method="post" id="testform">' . PHP_EOL
                . '<input type="hidden" name="_csrf_token" value="TOKEN" />' . PHP_EOL,
                '</form>' . PHP_EOL,
                array(
                    'text' => array(
                        0 => array(
                            'product_number' => '<input type="text" name="testform[0][product_number]" value="V001" id="testform_0_product_number" />' . PHP_EOL,
                            'product_desc' => '<input type="text" name="testform[0][product_desc]" value="Apple" id="testform_0_product_desc" />' . PHP_EOL,
                            'price' => '<input type="text" name="testform[0][price]" value="9.91" id="testform_0_price" />' . PHP_EOL
                        ),
                        1 => array(
                            'product_number' => '<input type="text" name="testform[1][product_number]" value="V002" id="testform_1_product_number" />' . PHP_EOL,
                            'product_desc' => '<input type="text" name="testform[1][product_desc]" value="Banana" id="testform_1_product_desc" />' . PHP_EOL,
                            'price' => '<input type="text" name="testform[1][price]" value="9.92" id="testform_1_price" />' . PHP_EOL
                        ),
                        2 => array(
                            'product_number' => '<input type="text" name="testform[2][product_number]" value="V003" id="testform_2_product_number" />' . PHP_EOL,
                            'product_desc' => '<input type="text" name="testform[2][product_desc]" value="Orange" id="testform_2_product_desc" />' . PHP_EOL,
                            'price' => '<input type="text" name="testform[2][price]" value="9.93" id="testform_2_price" />' . PHP_EOL
                        )
                    ),
                    'label' => array(
                        0 => array(
                            'product_number' => '<label for="testform_0_product_number">testform.product_number</label>' . PHP_EOL,
                            'product_desc' => '<label for="testform_0_product_desc">testform.product_desc</label>' . PHP_EOL,
                            'price' => '<label for="testform_0_price">testform.price</label>' . PHP_EOL,
                        ),
                        1 => array(
                            'product_number' => '<label for="testform_1_product_number">testform.product_number</label>' . PHP_EOL,
                            'product_desc' => '<label for="testform_1_product_desc">testform.product_desc</label>' . PHP_EOL,
                            'price' => '<label for="testform_1_price">testform.price</label>' . PHP_EOL,
                        ),
                        2 => array(
                            'product_number' => '<label for="testform_2_product_number">testform.product_number</label>' . PHP_EOL,
                            'product_desc' => '<label for="testform_2_product_desc">testform.product_desc</label>' . PHP_EOL,
                            'price' => '<label for="testform_2_price">testform.price</label>' . PHP_EOL,
                        )
                    ),
                    'error' => array(
                        0 => array(
                            'product_number' => null,
                            'product_desc' => null,
                            'price' => null,
                        ),
                        1 => array(
                            'product_number' => null,
                            'product_desc' => null,
                            'price' => null,
                        ),
                        2 => array(
                            'product_number' => null,
                            'product_desc' => null,
                            'price' => null,
                        )
                    )
                ),
                'valid dataset of product data with bind data'
            ),
            array(
                array($product1, $product2, $product3),
                array(
                    0 => array(
                        'product_number' => '',
                        'product_desc' => 'Apple',
                        'price' => '9.91'
                    ),
                    1 => array(
                        'product_number' => 'V002',
                        'product_desc' => 'Banana',
                        'price' => ''
                    ),
                    2 => array(
                        'product_number' => 'V003',
                        'product_desc' => 'Orange',
                        'price' => '9.93'
                    )
                ),
                '<form method="post" id="testform">' . PHP_EOL
                . '<input type="hidden" name="_csrf_token" value="TOKEN" />' . PHP_EOL,
                '</form>' . PHP_EOL,
                array(
                    'text' => array(
                        0 => array(
                            'product_number' => '<input type="text" name="testform[0][product_number]" value="" id="testform_0_product_number" class="nc_form_error" />' . PHP_EOL,
                            'product_desc' => '<input type="text" name="testform[0][product_desc]" value="Apple" id="testform_0_product_desc" />' . PHP_EOL,
                            'price' => '<input type="text" name="testform[0][price]" value="9.91" id="testform_0_price" />' . PHP_EOL
                        ),
                        1 => array(
                            'product_number' => '<input type="text" name="testform[1][product_number]" value="V002" id="testform_1_product_number" />' . PHP_EOL,
                            'product_desc' => '<input type="text" name="testform[1][product_desc]" value="Banana" id="testform_1_product_desc" />' . PHP_EOL,
                            'price' => '<input type="text" name="testform[1][price]" value="" id="testform_1_price" class="nc_form_error" />' . PHP_EOL
                        ),
                        2 => array(
                            'product_number' => '<input type="text" name="testform[2][product_number]" value="V003" id="testform_2_product_number" />' . PHP_EOL,
                            'product_desc' => '<input type="text" name="testform[2][product_desc]" value="Orange" id="testform_2_product_desc" />' . PHP_EOL,
                            'price' => '<input type="text" name="testform[2][price]" value="9.93" id="testform_2_price" />' . PHP_EOL
                        )
                    ),
                    'label' => array(
                        0 => array(
                            'product_number' => '<label for="testform_0_product_number">testform.product_number</label>' . PHP_EOL,
                            'product_desc' => '<label for="testform_0_product_desc">testform.product_desc</label>' . PHP_EOL,
                            'price' => '<label for="testform_0_price">testform.price</label>' . PHP_EOL,
                        ),
                        1 => array(
                            'product_number' => '<label for="testform_1_product_number">testform.product_number</label>' . PHP_EOL,
                            'product_desc' => '<label for="testform_1_product_desc">testform.product_desc</label>' . PHP_EOL,
                            'price' => '<label for="testform_1_price">testform.price</label>' . PHP_EOL,
                        ),
                        2 => array(
                            'product_number' => '<label for="testform_2_product_number">testform.product_number</label>' . PHP_EOL,
                            'product_desc' => '<label for="testform_2_product_desc">testform.product_desc</label>' . PHP_EOL,
                            'price' => '<label for="testform_2_price">testform.price</label>' . PHP_EOL,
                        )
                    ),
                    'error' => array(
                        0 => array(
                            'product_number' => '<span class="ncFormError">This value should not be blank.</span>' . PHP_EOL,
                            'product_desc' => null,
                            'price' => null,
                        ),
                        1 => array(
                            'product_number' => null,
                            'product_desc' => null,
                            'price' => '<span class="ncFormError">This value shoud be a decimal.</span>' . PHP_EOL,
                        ),
                        2 => array(
                            'product_number' => null,
                            'product_desc' => null,
                            'price' => null,
                        )
                    )
                ),
                'invalid dataset of product data with missing data'
            ),
            array(
                array($product1),
                array(
                    0 => array(
                        'product_number' => 'V001',
                        'product_desc' => 'Apple',
                        'price' => '9.91'
                    )
                ),
                '<form method="post" id="testform">' . PHP_EOL
                . '<input type="hidden" name="_csrf_token" value="TOKEN" />' . PHP_EOL,
                '</form>' . PHP_EOL,
                array(
                    'text' => array(
                        0 => array(
                            'product_number' => '<input type="text" name="testform[0][product_number]" value="V001" id="testform_0_product_number" />' . PHP_EOL,
                            'product_desc' => '<input type="text" name="testform[0][product_desc]" value="Apple" id="testform_0_product_desc" />' . PHP_EOL,
                            'price' => '<input type="text" name="testform[0][price]" value="9.91" id="testform_0_price" />' . PHP_EOL
                        )
                    ),
                    'label' => array(
                        0 => array(
                            'product_number' => '<label for="testform_0_product_number">testform.product_number</label>' . PHP_EOL,
                            'product_desc' => '<label for="testform_0_product_desc">testform.product_desc</label>' . PHP_EOL,
                            'price' => '<label for="testform_0_price">testform.price</label>' . PHP_EOL,
                        )
                    ),
                    'error' => array(
                        0 => array(
                            'product_number' => null,
                            'product_desc' => null,
                            'price' => null,
                        ),
                    )
                ),
                'one valid dataset of product data'
            )
        );
    }

    /**
     * @dataProvider formCollectionFieldProvider
     * @param	array		$entities			    entities of plain old php object
     * @param	array		$dataMap			    form data
     * @param	string		$expectedFormStart	    expected form start tag html
     * @param	string		$expectedFormEnd	    expected form end tag html
     * @param	array		$expectedFormHelpers	array of expected form helper html
     * @param	string		$description	        dataset description
     */
    public function testFieldFormCollection($entities, $dataMap, $expectedFormStart, $expectedFormEnd, $expectedFormHelpers, $description)
    {
        $configuration = new Configuration();
        $validator = new Validator($configuration);

        $form = new FormCollection($entities, 'testform', $configuration);
        $form->setValidator($validator);
        if (!is_null($dataMap)) {
            $payload = array();
            $payload['testform'] = $dataMap;
            $payload['_csrf_token'] = 'TOKEN';
            $form->bind($payload);
            $form->isValid();
        } else {
            $form->bind();
        }
        $formHelper = new FormHelper($form);

        $this->assertEquals($expectedFormStart, $formHelper->formStart());
        $this->assertEquals($expectedFormEnd, $formHelper->formEnd());

        $entityContainers = $form->getEntityContainerIterator();

        foreach ($expectedFormHelpers as $helperName => $expectedFormHelper) {
            $this->assertCount(count($expectedFormHelper), $entityContainers);
            foreach ($entityContainers as $entityName => $entityContainer) {
                $propertyCounter = 0;
                foreach ($expectedFormHelper[$entityName] as $property => $expectedFormField) {
                    $propertyCounter++;
                    $this->assertEquals($expectedFormField, $formHelper->formField($helperName, $property));
                }
                $this->assertEquals(count($expectedFormHelper[$entityName]), $propertyCounter, 'unexpected form property count');
            }
        }
    }

    /**
     * @dataProvider formCollectionFieldProvider
     * @param	array		$entities			    entities of plain old php object
     * @param	array		$dataMap			    form data
     * @param	string		$expectedFormStart	    expected form start tag html
     * @param	string		$expectedFormEnd	    expected form end tag html
     * @param	array		$expectedFormHelpers	array of expected form helper html
     * @param	string		$description	        dataset description
     */
    public function testFormCollectionIterator($entities, $dataMap, $expectedFormStart, $expectedFormEnd, $expectedFormHelpers, $description)
    {
        $configuration = new Configuration();
        $validator = new Validator($configuration);

        $form = new FormCollection($entities, 'testform', $configuration);
        $form->setValidator($validator);
        if (!is_null($dataMap)) {
            $payload = array();
            $payload['testform'] = $dataMap;
            $payload['_csrf_token'] = 'TOKEN';
            $form->bind($payload);
            $form->isValid();
        } else {
            $form->bind();
        }
        $formHelper = new FormHelper($form);

        $this->assertEquals($expectedFormStart, $formHelper->formStart());
        $this->assertEquals($expectedFormEnd, $formHelper->formEnd());

        foreach ($expectedFormHelpers as $helperName => $expectedFormHelper) {
            $this->assertCount(count($expectedFormHelper), $form);
            foreach ($form as $entityName => $entityContainer) {
                $propertyCounter = 0;
                foreach ($expectedFormHelper[$entityName] as $property => $expectedFormField) {
                    $propertyCounter++;
                    $this->assertEquals($expectedFormField, $formHelper->formField($helperName, $property));
                }
                $this->assertEquals(count($expectedFormHelper[$entityName]), $propertyCounter, 'unexpected form property count');
            }
        }
    }

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

        return $entityContainer;
    }

    /**
     * @depends   testInitFormEntity
     * @param     EntityContainerInterface  $entityContainer        form entity container
     */
    public function testFormHelperChoise(EntityContainerInterface $entityContainer)
    {
        $propertyObject = $entityContainer->getProperty('newsletter');

        $formHelper = new FormHelperChoiceCheckbox();
        $formHelper->setProperty($propertyObject);
        $formHelper->setChoice(1);
        $formHelper->render();

        $this->assertEquals('<input type="checkbox" name="testform[newsletter]" value="1" id="testform_newsletter" />' . PHP_EOL, $formHelper->render());

        $formHelper = new FormHelperChoiceRadio();
        $formHelper->setProperty($propertyObject);
        $formHelper->setChoice(1);
        $formHelper->render();

        $this->assertEquals('<input type="radio" name="testform[newsletter]" value="1" id="testform_newsletter" />' . PHP_EOL, $formHelper->render());

        $formHelper = new FormHelperChoiceSelect();
        $formHelper->setProperty($propertyObject);
        $formHelper->setChoice(1);
        $formHelper->render();

        $expectedString = '<select name="testform[newsletter]" id="testform_newsletter"><option>1</option></select>' . PHP_EOL;
        $this->assertEquals($expectedString, $formHelper->render());

        $formHelper = new FormHelperChoiceSelect();
        $formHelper->setProperty($propertyObject);
        $formHelper->setChoices(array(0 => 'unsubscribe', 1 => 'subscribe'));
        $formHelper->render();

        $expectedString = '<select name="testform[newsletter]" id="testform_newsletter">';
        $expectedString .= '<option value="0" selected="selected">unsubscribe</option>';
        $expectedString .= '<option value="1">subscribe</option>';
        $expectedString .= '</select>' . PHP_EOL;
        $this->assertEquals($expectedString, $formHelper->render());
    }
}