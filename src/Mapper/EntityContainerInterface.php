<?php
/*
 * Copyright 2008 Sven Sanzenbacher
 *
 * This file is part of the naucon package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Naucon\Form\Mapper;

use Naucon\Form\FormInterface;
use Naucon\Form\Error\FormErrorInterface;

/**
 * Form Entity Container Interface
 *
 * @package     Form
 * @subpackage  Mapper
 * @author      Sven Sanzenbacher
 */
interface EntityContainerInterface
{
    /**
     * return to what form the entity is bind
     *
     * @return    FormInterface     form instance
     */
    public function getForm();

    /**
     * return entity name
     *
     * @return    string             entity name
     */
    public function getName();

    /**
     * return if the entity shell be validated
     *
     * @return    bool      true = entity will be validated, false = skip validation
     */
    public function hasValidate();

    /**
     * define if the entity shell be validated
     *
     * @param     bool      $bool       true = entity will be validated, false = skip validation
     */
    public function setValidate($bool);

    /**
     * return entity
     *
     * @return    object             entity
     */
    public function getEntity();

    /**
     * @param     string        $name       property name
     * @return    string        form name
     */
    public function getFormName($name);

    /**
     * @param     string        $name       property name
     * @return    string        form value
     */
    public function getFormValue($name);

    /**
     * get form property
     *
     * @param     string    $propertyName       form property name
     * @return    Property
     */
    public function getProperty($propertyName);

    /**
     * add form property
     *
     * @param     Property      $property       form property instance
     */
    public function attachProperty(Property $property);

    /**
     * remove form property
     *
     * @param     string        $name       property name
     */
    public function detachProperty($name);

    /**
     * return entity errors
     *
     * @return    array             entity errors
     */
    public function getErrors();

    /**
     * has entity errors
     *
     * @return     bool
     */
    public function hasErrors();

    /**
     * return entity error of a given key
     *
     * @param     string        $key        key
     * @return    FormErrorInterface        form error instance
     */
    public function getError($key);

    /**
     * has entity error of given key
     *
     * @param     string        $key        key
     * @return    bool
     */
    public function hasError($key);

    /**
     * set entity error for a given key
     *
     * @param     string        $key        key
     * @param     FormErrorInterface    $formError      form error instance
     */
    public function setError($key, FormErrorInterface $formError);

    /**
     * invoke hook
     *
     * calls a hook method of the entity
     *
     * @param     string        $methodName            hook method name
     */
    public function invokeHook($methodName);
}