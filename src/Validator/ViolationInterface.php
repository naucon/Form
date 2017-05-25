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
 * Violation Interface
 *
 * @package     Form
 * @subpackage  Validator
 * @author      Sven Sanzenbacher
 */
interface ViolationInterface
{
    /**
     * @return  string      property name
     */
    public function getName();

    /**
     * @return  mixed       violation value
     */
    public function getValue();

    /**
     * @return  string      violation message
     */
    public function getMessage();
}