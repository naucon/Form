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

use Naucon\Form\FormCollectionInterface;

/**
 * Form Helper Option Interface
 *
 * @package     Form
 * @subpackage  Helper
 * @author      Sven Sanzenbacher
 */
interface FormHelperOptionInterface extends FormHelperInterface
{
    /**
     * @return    FormCollectionInterface
     */
    public function getForm();

    /**
     * @param     FormCollectionInterface       $form       form collection instance
     */
    public function setForm(FormCollectionInterface $form);

    /**
     * @return    string
     */
    public function getChoice();

    /**
     * @param     string        $choice
     */
    public function setChoice($choice);

    /**
     * @return    array
     */
    public function getChoices();

    /**
     * @param     array         $choices
     */
    public function setChoices(array $choices = []);
}
