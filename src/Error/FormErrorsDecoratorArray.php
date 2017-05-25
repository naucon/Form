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
 * Form Errors Array Decorator
 *
 * @package     Form
 * @subpackage  Error
 * @author      Sven Sanzenbacher
 */
class FormErrorsDecoratorArray extends FormErrorsDecoratorAbstract
{
    /**
     * @return     array
     */
    public function getErrors()
    {
        $errors = array();
        $formErrors = $this->getForm()->getErrors();

        foreach ($formErrors as $formKey => $formError) {
            if ($formError instanceof FormErrorInterface) {
                $errors[$this->getForm()->getName()][$formError->getName()] = $formError->getMessage();
            } else {
                /**
                 * @var FormErrorInterface $formErr
                 */
                foreach ($formError as $formErr) {
                    $errors[$this->getForm()->getName()][$formKey][$formErr->getName()] = $formErr->getMessage();
                }
            }
        }
        return $errors;
    }
}