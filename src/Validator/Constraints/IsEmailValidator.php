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

use Egulias\EmailValidator\EmailValidator;
use Egulias\EmailValidator\Validation\RFCValidation;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

/**
 * Is Email address validator
 *
 * @package    Form
 * @subpackage  Validator
 * @author     Sven Sanzenbacher
 */
class IsEmailValidator extends ConstraintValidator
{
    /**
     * {@inheritdoc}
     */
    public function validate($value, Constraint $constraint)
    {
        if ($value === null || ($value === '' && !$constraint->isMandatory)) {
            return;
        }

        if (!($constraint instanceof IsEmail)) {
            throw new UnexpectedTypeException($constraint, IsEmail::class);
        }

        $validator = new EmailValidator();

        if (is_string($value) && $validator->isValid($value, new RFCValidation())) {
            return;
        }

        $this->context
            ->buildViolation($constraint->message)
            ->addViolation()
        ;
    }
}
