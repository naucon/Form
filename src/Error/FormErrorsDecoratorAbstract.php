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
 * Abstract Form Errors Decorator
 *
 * @package     Form
 * @subpackage  Error
 * @author      Sven Sanzenbacher
 */
abstract class FormErrorsDecoratorAbstract implements FormErrorsDecoratorInterface
{
    /**
     * @var    FormInterface
     */
    protected $form;


    /**
     * Constructor
     *
     * @param    FormInterface      $form       form instance
     */
    public function __construct(FormInterface $form)
    {
        $this->setForm($form);
    }

    /**
     * @return    FormInterface
     */
    public function getForm()
    {
        return $this->form;
    }

    /**
     * @param     FormInterface     $form       form instance
     */
    public function setForm(FormInterface $form)
    {
        $this->form = $form;
    }
}