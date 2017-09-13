<?php
/*
 * Copyright 2008 Sven Sanzenbacher
 *
 * This file is part of the naucon package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Naucon\Form;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Naucon\Utility\ArrayPath;

/**
 * Configuration
 *
 * @package    Form
 * @author     Sven Sanzenbacher
 */
class Configuration
{
    /**
     * @var     \Naucon\Utility\ArrayPath
     */
    protected $config;



    /**
     * Constructor
     *
     * @param   array   $options         configuration
     */
    public function __construct(array $options=array())
    {
        $resolver = new OptionsResolver();
        $this->configureOptions($resolver);

        $config = $resolver->resolve($options);
        $this->config = new ArrayPath($config);
    }

    /**
     * prepare default configuration
     *
     * @param       OptionsResolver      $resolver
     */
    protected function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'csrf_parameter' => '_csrf_token',
            'csrf_protection' => true,
            'collection_type' => FormCollectionInterface::COLLECTION_TYPE_ALL,
            'locale' => 'en_EN',
            'fallback_locales' => array('en_EN'),
            'translator_paths' => array(),
            'config_paths' => array(),
            'validation_groups' => null
        ));

        $resolver->setAllowedValues('collection_type', array(FormCollectionInterface::COLLECTION_TYPE_ALL, FormCollectionInterface::COLLECTION_TYPE_ANY, FormCollectionInterface::COLLECTION_TYPE_MANY, FormCollectionInterface::COLLECTION_TYPE_ONE));
    }

    /**
     * return the value of the given key
     *
     * @param   string      $key        config key
     * @return   mixed       config value
     */
    public function get($key)
    {
        return $this->config->get($key);
    }

    /**
     * set new value for a given key
     *
     * @param   string      $key        config key
     * @param   array       $value      config value
     */
    protected function set($key, $value)
    {
        $this->config->set($key, $value);
    }

    /**
     * return all
     *
     * @return  array       config
     */
    public function all()
    {
        return $this->config->get();
    }
}