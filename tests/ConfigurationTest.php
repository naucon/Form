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

use Naucon\Form\FormCollectionInterface;
use Naucon\Form\Configuration;
use PHPUnit\Framework\TestCase;

class ConfigurationTest extends TestCase
{
    public function configProvider(): array
    {
        return array(
            array(
                array(),
                array(
                    'csrf_parameter' => '_csrf_token',
                    'csrf_protection' => true,
                    'collection_type' => FormCollectionInterface::COLLECTION_TYPE_ALL,
                    'locale' => 'en_EN',
                    'fallback_locales' => array('en_EN'),
                    'translator_paths' => array(),
                    'config_paths' => array(),
                    'validation_groups' => null
                ),
            ),
            array(
                array('csrf_parameter' => '_foo_csrf_token'),
                array('csrf_parameter' => '_foo_csrf_token',
                    'csrf_protection' => true,
                    'collection_type' => FormCollectionInterface::COLLECTION_TYPE_ALL,
                    'locale' => 'en_EN',
                    'fallback_locales' => array('en_EN'),
                    'translator_paths' => array(),
                    'config_paths' => array(),
                    'validation_groups' => null
                ),
            ),
            array(
                array('csrf_protection' => false),
                array('csrf_parameter' => '_csrf_token',
                    'csrf_protection' => false,
                    'collection_type' => FormCollectionInterface::COLLECTION_TYPE_ALL,
                    'locale' => 'en_EN',
                    'fallback_locales' => array('en_EN'),
                    'translator_paths' => array(),
                    'config_paths' => array(),
                    'validation_groups' => null
                ),
            ),
            array(
                array('locale' => 'de_DE'),
                array('csrf_parameter' => '_csrf_token',
                    'csrf_protection' => true,
                    'collection_type' => FormCollectionInterface::COLLECTION_TYPE_ALL,
                    'locale' => 'de_DE',
                    'fallback_locales' => array('en_EN'),
                    'translator_paths' => array(),
                    'config_paths' => array(),
                    'validation_groups' => null
                ),
            ),
            array(
                array('locale' => 'de_DE', 'fallback_locales' => array('de_DE', 'en_EN')),
                array('csrf_parameter' => '_csrf_token',
                    'csrf_protection' => true,
                    'collection_type' => FormCollectionInterface::COLLECTION_TYPE_ALL,
                    'locale' => 'de_DE',
                    'fallback_locales' => array('de_DE', 'en_EN'),
                    'translator_paths' => array(),
                    'config_paths' => array(),
                    'validation_groups' => null
                ),
            ),
            array(
                array('validation_groups' => array('Default')),
                array(
                    'csrf_parameter' => '_csrf_token',
                    'csrf_protection' => true,
                    'collection_type' => FormCollectionInterface::COLLECTION_TYPE_ALL,
                    'locale' => 'en_EN',
                    'fallback_locales' => array('en_EN'),
                    'translator_paths' => array(),
                    'config_paths' => array(),
                    'validation_groups' => array('Default')
                ),
            ),
            array(
                array('locale' => 'de_DE', 'fallback_locales' => array('de_DE', 'en_EN'), 'validation_groups' => array('Default', 'registration')),
                array('csrf_parameter' => '_csrf_token',
                    'csrf_protection' => true,
                    'collection_type' => FormCollectionInterface::COLLECTION_TYPE_ALL,
                    'locale' => 'de_DE',
                    'fallback_locales' => array('de_DE', 'en_EN'),
                    'translator_paths' => array(),
                    'config_paths' => array(),
                    'validation_groups' => array('Default', 'registration')
                ),
            )
        );
    }

    /**
     * @dataProvider configProvider
     * @param    array      $config             config settings
     * @param    array      $expectedConfig     expected form configuration
     */
    public function testMerge($config, $expectedConfig)
    {
        $configuration = new Configuration($config);
        $actualConfig = $configuration->all();

        $this->assertEquals($expectedConfig, $actualConfig);
    }

    /**
     * @dataProvider configProvider
     * @param    array      $config             config settings
     * @param    array      $expectedConfig     expected form configuration
     */
    public function testGet($config, $expectedConfig)
    {
        $configuration = new Configuration($config);

        $count = 0;
        foreach ($expectedConfig as $key => $value) {
            $count++;
            $this->assertEquals($value, $configuration->get($key), 'unexpected config entry');
        }

        $this->assertEquals(count($expectedConfig), $count, 'unexpected config count');
    }
}
