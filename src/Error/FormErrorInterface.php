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
 * Form Error Interface
 *
 * @package     Form
 * @subpackage  Error
 * @author      Sven Sanzenbacher
 */
interface FormErrorInterface
{
    /**
     * @return    string            property name
     */
    public function getName();

    /**
     * @param     string        $name       property name
     */
    public function setName($name);

    /**
     * @return    string            message
     */
    public function getMessage();

    /**
     * @param     string        $message        message
     */
    public function setMessage($message);
}