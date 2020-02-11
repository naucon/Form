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
use Naucon\Utility\ArrayPath;

/**
 * Abstract Form Helper Tag
 *
 * @abstract
 * @package     Form
 * @subpackage  Helper
 * @author      Sven Sanzenbacher
 */
abstract class AbstractFormHelperTag implements FormHelperTagInterface
{
    /**
     * @var    FormInterface
     */
    protected $form;

    /**
     * @var    string
     */
    protected $content = null;

    /**
     * @var    \Naucon\Utility\ArrayPath        helper options
     */
    protected $options;


    /**
     * @return    FormInterface
     */
    public function getForm()
    {
        return $this->form;
    }

    /**
     * @param     FormInterface       $form       form instance
     */
    public function setForm(FormInterface $form)
    {
        $this->form = $form;
    }

    /**
     * @return    string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @param     string        $content
     */
    public function setContent($content)
    {
        $this->content = $content;
    }

    /**
     * @return    \Naucon\Utility\ArrayPath
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
    public function setOptions(array $options = [])
    {
        $this->getOptions()->setAll($options);
    }
}
