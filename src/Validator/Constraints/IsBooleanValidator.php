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
 * Is boolean validator
 *
 * @package    Form
 * @subpackage  Validator
 * @author     Sven Sanzenbacher
 */
class IsBooleanValidator extends ConstraintValidator
{
    /**
     * {@inheritdoc}
     */
    public function validate($value, Constraint $constraint)
    {
        if (is_bool($value)) {
            return;
        }

        if ($value === 1) {
            return;
        }

        if ($value === 0) {
            return;
        }

        if ($value === '1') {
            return;
        }

        if ($value === '0') {
            return;
        }

        if ($value === null) {
            return;
        }

        $this->context->buildViolation($constraint->message)
            ->addViolation();
    }
}
