<?php
/*
 * Copyright 2008 Sven Sanzenbacher
 *
 * This file is part of the naucon package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Naucon\Form\Helper;

use Naucon\Form\FormInterface;

/**
 * Form Helper Tag Interface
 *
 * @package     Form
 * @subpackage  Helper
 * @author      Sven Sanzenbacher
 */
interface FormHelperTagInterface extends FormHelperInterface
{
    /**
     * @return    FormInterface
     */
    public function getForm();

    /**
     * @param     FormInterface       $form       form instance
     */
    public function setForm(FormInterface $form);
}