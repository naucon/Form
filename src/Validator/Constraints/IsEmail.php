<?php
/*
 * Copyright 2008 Sven Sanzenbacher
 *
 * This file is part of the naucon package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Naucon\Form\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * Is Email address contraint
 *
 * @package     Form
 * @subpackage  Validator
 * @author      Sven Sanzenbacher
 * @Annotation
 */
class IsEmail extends Constraint
{
    public $message = 'This value is not a valid email address.';
}
