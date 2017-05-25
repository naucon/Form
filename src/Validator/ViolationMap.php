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

use Naucon\Utility\Map;

/**
 * Violation Map
 *
 * @package     Form
 * @subpackage  Validator
 * @author      Sven Sanzenbacher
 */
class ViolationMap implements ViolationMapInterface
{
    /**
     * @var    /Naucon/Utility/Map
     */
    protected $violationMap;


    /**
     * return map of violations
     *
     * @return    \Naucon\Utility\Map
     */
    protected function getMap()
    {
        if (is_null($this->violationMap)) {
            $this->violationMap = new Map();
        }
        return $this->violationMap;
    }

    /**
     * return all violations
     *
     * @return    array     violations
     */
    public function getAll()
    {
        return $this->getMap()->getAll();
    }

    /**
     * has any violations
     *
     * @return    bool      has any violations
     */
    public function hasViolations()
    {
        if ($this->getMap()->count() > 0) {
            return true;
        }
        return false;
    }

    /**
     * return violations for an given property name
     *
     * @param     string        $propertyName       property name
     * @return    null|array|ViolationInterface
     */
    public function get($propertyName)
    {
        return $this->getMap()->get($propertyName);
    }

    /**
     * has violations for a given property name
     *
     * @param     string        $propertyName       property name
     * @return    bool
     */
    public function has($propertyName)
    {
        return $this->getMap()->hasKey($propertyName);
    }

    /**
     * add violations for a given property name
     *
     * @param     string        $propertyName       key
     * @param     ViolationInterface    $violation      violation instance
     */
    public function set($propertyName, ViolationInterface $violation)
    {
        $this->getMap()->set($propertyName, $violation);
    }
}