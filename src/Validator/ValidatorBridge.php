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

use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface as BaseValidatorInterface;
use Symfony\Component\Validator\Validation as BaseValidator;
use Symfony\Contracts\Translation\TranslatorInterface as BaseTranslatorInterface;
use Naucon\Form\Exception\InvalidArgumentException;
use Naucon\Form\Utility\Utility;

/**
 * Validator Bridge to Symfony Validator Component
 *
 * @package     Form
 * @subpackage  Validator
 * @author      Sven Sanzenbacher
 */
class ValidatorBridge implements ValidatorInterface
{
    /**
     * @var     BaseValidatorInterface       validator handler
     */
    protected $handler = null;


    /**
     * Constructor
     *
     * @param BaseValidatorInterface|null $handler validator handler
     * @param BaseTranslatorInterface|null $translator translator handler
     */
    public function __construct(BaseValidatorInterface $handler=null, BaseTranslatorInterface $translator=null)
    {
        if (is_null($handler)) {
            $validatorBuilder = BaseValidator::createValidatorBuilder();
            $validatorBuilder->addMethodMapping('loadValidatorMetadata');
            if (!is_null($translator)) {
                $validatorBuilder->setTranslator($translator);
                $validatorBuilder->setTranslationDomain('validators');
            }
            $handler = $validatorBuilder->getValidator();
        }

        $this->setHandler($handler);
    }



    /**
     * @return  BaseValidatorInterface       validator handler
     */
    protected function getHandler()
    {
        return $this->handler;
    }

    /**
     * @param BaseValidatorInterface $handler      validator handler
     */
    protected function setHandler(BaseValidatorInterface $handler)
    {
        $this->handler = $handler;
    }

    /**
     * validate a given entity
     *
     * a entity is validated. Violations are wrapped in a instance of ViolationInterface
     *
     * @param object $entity
     * @param array|null $constraints
     * @param array|null $groups
     * @return array    array of violations, wrapped in a instance of ViolationInterface
     */
    public function validate($entity, $constraints = null, $groups = null)
    {
        if (!is_object($entity)) {
            throw new InvalidArgumentException('validator has no valid entity');
        }

        $result = [];
        $violations = $this->getHandler()->validate($entity, $constraints, $groups);

        /**
         * @var ConstraintViolationInterface $violation
         */
        foreach ($violations as $violation) {
            $propertyName = Utility::normilizeName($violation->getPropertyPath());

            $result[] = new Violation($propertyName, $violation->getInvalidValue(), $violation->getMessage());
        }

        return $result;
    }

}
