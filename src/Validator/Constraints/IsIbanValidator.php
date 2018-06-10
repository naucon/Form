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
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\ConstraintDefinitionException;
use Naucon\Iban\Iban;

/**
 * Is IBAN validator
 * ISO 7064 (Modulo 97-10)
 *
 * @package    Form
 * @subpackage  Validator
 * @author     Sven Sanzenbacher
 */
class IsIbanValidator extends ConstraintValidator
{
    /**
     * {@inheritdoc}
     */
    public function validate($value, Constraint $constraint)
    {
        if ($value === null) {
            return;
        }

        if (!$constraint instanceof IsIban) {
            throw new UnexpectedTypeException($constraint, __NAMESPACE__ . '\IsIban');
        }

        $countryCodes = null;
        if (is_array($constraint->countryCodes)) {
            if (count($constraint->countryCodes) == 0) {
                throw new ConstraintDefinitionException('Given country code on IBAN contrain is empty');
            }

            $countryCodes = $constraint->countryCodes;
        }

        if (is_string($constraint->countryCodes)) {
            if (empty($constraint->countryCodes)) {
                throw new ConstraintDefinitionException('Given country code on IBAN contrain is empty');
            }

            $countryCodes = array($constraint->countryCodes);
        }

        $type = new Iban($value);
        if ($type->isValid()) {
            if (!is_null($countryCodes)) {
                if (in_array($type->getCountryCode(), $countryCodes)) {
                    return;
                }
            } else {
                return;
            }
        }

        $this->context->buildViolation($constraint->message)
            ->addViolation();

        return;
    }
}
