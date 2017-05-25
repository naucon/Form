<?php
/*
 * Copyright 2008 Sven Sanzenbacher
 *
 * This file is part of the naucon package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Naucon\Form\Validator;

/**
 * Violation
 *
 * @package     Form
 * @subpackage  Validator
 * @author      Sven Sanzenbacher
 */
class Violation implements ViolationInterface
{
    /**
     * @var     string      property name
     */
    protected $name;

    /**
     * @var     string      property value
     */
    protected $value;

    /**
     * @var     string      violation message
     */
    protected $message;



    /**
     * Constructor
     *
     * @param   string      $name       property name
     * @param   string      $value      property value
     * @param   string      $message        violation message
     */
    public function __construct($name, $value, $message)
    {
        $this->setName($name);
        $this->setValue($value);
        $this->setMessage($message);
    }



    /**
     * @return  string      property name
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param   string      $name       property name
     */
    protected function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return  mixed       property value
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param   string      $value      property value
     */
    protected function setValue($value)
    {
        $this->value = $value;
    }

    /**
     * @return  string      violation message
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @param   string      $message        violation message
     */
    protected function setMessage($message)
    {
        $this->message = $message;
    }
}