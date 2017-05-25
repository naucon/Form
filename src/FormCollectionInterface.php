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

/**
 * Form Collection Interface
 *
 * @package    Form
 * @author     Sven Sanzenbacher
 */
interface FormCollectionInterface extends FormInterface
{
    const COLLECTION_TYPE_ALL = 'all';
    const COLLECTION_TYPE_ONE = 'one';
    const COLLECTION_TYPE_MANY = 'many';


    /**
     * Constructor
     *
     * @param     array         $entities           array of entities
     * @param     string        $name               dataset name
     * @param     Configuration       $configuration
     */
    public function __construct(array $entities, $name, Configuration $configuration);

    /**
     * return form option key
     *
     * @return    string                    form option key
     */
    public function getFormOptionKey();

    /**
     * set form option key
     *
     * @param     string        $type       form option key
     */
    public function setFormOptionKey($type);

    /**
     * return form option default value
     *
     * @return    array                     form option default values
     */
    public function getFormOptionDefaultValues();

    /**
     * set form option default value
     *
     * @param     array     $defaultValues      form option default values
     */
    public function setFormOptionDefaultValues(array $defaultValues);

    /**
     * add form option default value
     *
     * @param     string    $formEntityName     form entity name as form option default value
     */
    public function addFormOptionDefaultValues($formEntityName);

    /**
     * @return    string                    form name of form option
     */
    public function getFormOptionName();

    /**
     * @param     string        $name       form name
     * @return    bool
     */
    public function isFormOptionSelected($name);
}