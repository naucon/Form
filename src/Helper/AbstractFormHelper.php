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
use Naucon\Utility\IteratorDecoratorAbstract;

/**
 * Abstract Form Helper
 *
 * @abstract
 * @package     Form
 * @subpackage  Helper
 * @author      Sven Sanzenbacher
 */
abstract class AbstractFormHelper extends IteratorDecoratorAbstract
{
    /**
     * @var    FormInterface
     */
    protected $form;


    /**
     * Constructor
     *
     * @param     FormInterface     $form       form instance
     */
    public function __construct(FormInterface $form)
    {
        $this->setForm($form);

        parent::__construct($form->getEntityContainerIterator());
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