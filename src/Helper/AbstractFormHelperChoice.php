<?php
/*
 * Copyright 2008 Sven Sanzenbacher
 *
 * This file is part of the naucon package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Naucon\Form\Helper;

use Naucon\Form\Mapper\Property;
use Naucon\Utility\ArrayPath;

/**
 * Abstract Form Helper Field
 *
 * @abstract
 * @package     Form
 * @subpackage  Helper
 * @author      Sven Sanzenbacher
 */
abstract class AbstractFormHelperChoice implements FormHelperChoiceInterface
{
    /**
     * @var    Property
     */
    protected $property = null;

    /**
     * @var    ArrayPath        helper options
     */
    protected $options = null;

    /**
     * @var    string
     */
    protected $choice = null;

    /**
     * @var    array
     */
    protected $choices = array();

    /**
     * @var    array
     */
    protected $attributeWhitelist = [];


    /**
     * Constructor
     */
    public function __construct()
    {
        $this->attributeWhitelist = array('id', 'class', 'style');
    }


    /**
     * @return    Property
     */
    public function getProperty()
    {
        return $this->property;
    }

    /**
     * @param     Property      $property       from property instance
     */
    public function setProperty(Property $property)
    {
        $this->property = $property;
    }

    /**
     * @return    string
     */
    public function getChoice()
    {
        return $this->choice;
    }

    /**
     * @param     string        $choice
     */
    public function setChoice($choice)
    {
        $this->choice = $choice;
    }

    /**
     * @return    array
     */
    public function getChoices()
    {
        return $this->choices;
    }

    /**
     * @param     array     $choices
     */
    public function setChoices(array $choices=array())
    {
        $this->choices = $choices;
    }

    /**
     * @return    \Naucon\Utility\ArrayPath
     */
    public function getOptions()
    {
        if (is_null($this->options)) {
            $this->options = new ArrayPath();
        }
        return $this->options;
    }

    /**
     * @param     array     $options        helper options
     */
    public function setOptions(array $options=array())
    {
        $this->getOptions()->setAll($options);
    }

    /**
     * @param string $attributeName
     * @return bool
     */
    protected function isAttributeInjectable($attributeName)
    {
        if (in_array($attributeName, $this->attributeWhitelist)) {
            return true;
        }
        return false;
    }

    /**
     * @param array $attributeWhitelist
     */
    public function setAttributeWhitelist(array $attributeWhitelist)
    {
        $this->attributeWhitelist = $attributeWhitelist;
    }
}