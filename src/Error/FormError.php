<?php
/*
 * Copyright 2008 Sven Sanzenbacher
 *
 * This file is part of the naucon package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Naucon\Form\Error;

/**
 * Form Error
 *
 * @package     Form
 * @subpackage  Error
 * @author      Sven Sanzenbacher
 */
class FormError implements FormErrorInterface
{
    /**
     * @var    string       property name
     */
    protected $name;

    /**
     * @var    string       message
     */
    protected $message;


    /**
     * Constructor
     *
     * @param    string     $name           property name
     * @param    string     $message        message
     */
    public function __construct($name, $message)
    {
        $this->setName($name);
        $this->setMessage($message);
    }


    /**
     * @return    string            property name
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param     string        $name       property name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return    string            message
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @param     string        $message        message
     */
    public function setMessage($message)
    {
        $this->message = $message;
    }
}