<?php
/*
 * Copyright 2008 Sven Sanzenbacher
 *
 * This file is part of the naucon package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Naucon\Form;

use Naucon\Form\Error\FormError;
use Naucon\Form\Error\FormErrorInterface;
use Naucon\Form\Mapper\EntityContainerInterface;

/**
 * Form Hook
 *
 * @package    Form
 * @author     Sven Sanzenbacher
 */
class FormHook
{
    /**
     * @var    EntityContainerInterface
     */
    protected $entityContainer = null;

    /**
     * Constructor
     *
     * @param    EntityContainerInterface       $entityContainer        $form entity container
     */
    public function __construct(EntityContainerInterface $entityContainer)
    {
        $this->setEntityContainer($entityContainer);
    }

    /**
     * @access    protected
     * @return    EntityContainerInterface
     */
    protected function getEntityContainer()
    {
        return $this->entityContainer;
    }

    /**
     * @access    protected
     * @param     EntityContainerInterface      $entityContainer        form entity container
     */
    protected function setEntityContainer(EntityContainerInterface $entityContainer)
    {
        $this->entityContainer = $entityContainer;
    }

    /**
     * return entity errors
     *
     * @return    array             entity errors
     */
    public function getErrors()
    {
        return $this->getEntityContainer()->getErrors();
    }

    /**
     * has entity errors
     *
     * @return    bool
     */
    public function hasErrors()
    {
        return $this->getEntityContainer()->hasErrors();
    }

    /**
     * return entity error of a given key
     *
     * @param     string        $key        key
     * @return    FormErrorInterface
     */
    public function getError($key)
    {
        return $this->getEntityContainer()->getError($key);
    }

    /**
     * has entity error of given key
     *
     * @param     string        $key        key
     * @return    bool
     */
    public function hasError($key)
    {
        return $this->getEntityContainer()->hasError($key);
    }

    /**
     * @param     string        $key        form property name
     * @param     string        $message        error message
     */
    public function setError($key, $message)
    {
        $this->getEntityContainer()->setError($key, new FormError($key, $message));
    }
}