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
 * Validator Aware Interface
 *
 * @package     Form
 * @subpackage  Validator
 * @author      Sven Sanzenbacher
 */
interface ValidatorAwareInterface
{
    /**
     * define validator
     *
     * @param    ValidatorInterface    $validator      validator
     */
    public function setValidator(ValidatorInterface $validator);
}
