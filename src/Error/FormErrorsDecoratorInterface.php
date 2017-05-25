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

use Naucon\Form\FormInterface;

/**
 * Form Errors Decorator Interface
 *
 * @package     Form
 * @subpackage  Error
 * @author      Sven Sanzenbacher
 */
interface FormErrorsDecoratorInterface
{
    /**
     * Constructor
     *
     * @param     FormInterface     $form       form instance
     */
    public function __construct(FormInterface $form);

    /**
     * @return    FormInterface
     */
    public function getForm();

    /**
     * @return    array     array of form errors
     */
    public function getErrors();
}
