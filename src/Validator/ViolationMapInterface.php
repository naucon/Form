<?php
/*
 * Copyright 2008 Sven Sanzenbacher
 *
 * This file is part of the naucon package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Naucon\Form\Validator;

/**
 * Violation Map Interface
 *
 * @package     Form
 * @subpackage  Validator
 * @author      Sven Sanzenbacher
 */
interface ViolationMapInterface
{
    /**
     * return all violations
     *
     * @return    array     violations
     */
    public function getAll();

    /**
     * has any violations
     *
     * @return    bool      has any violations
     */
    public function hasViolations();

    /**
     * return violations for an given property name
     *
     * @param     string        $propertyName       property name
     * @return    null|array|ViolationInterface
     */
    public function get($propertyName);

    /**
     * has violations for a given property name
     *
     * @param     string        $propertyName       property name
     * @return    bool
     */
    public function has($propertyName);

    /**
     * add violations for a given property name
     *
     * @param     string            $propertyName       key
     * @param     ViolationInterface        $violation      violation instance
     */
    public function set($propertyName, ViolationInterface $violation);
}