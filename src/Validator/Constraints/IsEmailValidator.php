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
 * Is Email address validator
 *
 * @package    Form
 * @subpackage  Validator
 * @author     Sven Sanzenbacher
 */
class IsEmailValidator extends ConstraintValidator
{
    const REGEXP = "/^([a-zA-Z0-9_\.\-]+\@([a-zA-Z0-9\-]+\.)+[a-zA-Z0-9]{2,4}+,?)+$/";

    /**
     * {@inheritdoc}
     */
    public function validate($value, Constraint $constraint)
    {
        if ($value === null) {
            return;
        }

        if (is_string($value) && preg_match(self::REGEXP, $value)) {
            return;
        }

        $this->context
            ->buildViolation($constraint->message)
            ->addViolation()
        ;
    }
}
