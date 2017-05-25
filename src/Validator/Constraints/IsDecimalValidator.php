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
use Symfony\Component\Validator\ConstraintValidator;

/**
 * Is decimal validator
 *
 * @package    Form
 * @subpackage  Validator
 * @author     Sven Sanzenbacher
 */
class IsDecimalValidator extends ConstraintValidator
{
    /**
     * {@inheritdoc}
     */
    public function validate($value, Constraint $constraint)
    {
        $regexp = '/^([-]{0,1})(\d{1,}\.\d{1,})$/';

        if (is_float($value)) {
            return;
        }

        if (is_string($value) && preg_match($regexp, $value)) {
            return;
        }

        $this->context->buildViolation($constraint->message)
            ->addViolation();
    }
}
