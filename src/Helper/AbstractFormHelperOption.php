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
use Naucon\Utility\ArrayPath;

/**
 * Abstract Form Helper Option
 *
 * @abstract
 * @package     Form
 * @subpackage  Helper
 * @author      Sven Sanzenbacher
 */
abstract class AbstractFormHelperOption implements FormHelperOptionInterface
{
    /**
     * @var    FormCollectionInterface
     */
    protected $form;

    /**
     * @var    ArrayPath        helper options
     */
    protected $options;

    /**
     * @var    string
     */
    protected $choice;

    /**
     * @var    array
     */
    protected $choices = array();


    /**
     * @return    FormCollectionInterface
     */
    public function getForm()
    {
        return $this->form;
    }

    /**
     * @param     FormCollectionInterface       $form       form collection instance
     */
    public function setForm(FormCollectionInterface $form)
    {
        $this->form = $form;
    }

    /**
     * @return    \Naucon\Utility\ArrayPath     helper options
     */
    public function getOptions()
    {
        if (is_null($this->options)) {
            $this->options = new ArrayPath();
        }
        return $this->options;
    }

    /**
     * @param     array     $options        helper options
     */
    public function setOptions(array $options=array())
    {
        $this->getOptions()->setAll($options);
    }

    /**
     * @return    string
     */
    public function getChoice()
    {
        return $this->choice;
    }

    /**
     * @param     string        $choice
     */
    public function setChoice($choice)
    {
        $this->choice = $choice;
    }

    /**
     * @return    array
     */
    public function getChoices()
    {
        return $this->choices;
    }

    /**
     * @param     array         $choices
     */
    public function setChoices(array $choices=array())
    {
        $this->choices = $choices;
    }
}