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

use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;

/**
 * Form Property Mapper
 *
 * @package		Form
 * @author		Sven Sanzenbacher
 */
class PropertyMapper
{
    /**
     * @var     PropertyAccessorInterface
     */
    protected $propertyAccessor;


    /**
     * Constructor
     */
    public function __construct()
    {
        $this->propertyAccessor = PropertyAccess::createPropertyAccessor();
    }

    /**
     * @param   object      $entity         entities
     * @param   array       $payload        submitted form data
     * @return  object      $entity         entities
     */
    public function mapFormToData($entity, array $payload)
    {
        foreach ($payload as $key => $value) {
            if ($this->propertyAccessor->isWritable($entity, $key)) {
                $this->propertyAccessor->setValue($entity, $key, $value);
            }
        }

        return $entity;
    }

    /**
     * @param   object      $entity             entity
     * @param   string      $propertyName       form property name
     * @return  mixed       form property value
     */
    public function mapDataToForm($entity, $propertyName)
    {
        $value = null;
        if ($this->propertyAccessor->isReadable($entity, $propertyName)) {
            $value = $this->propertyAccessor->getValue($entity, $propertyName);
        }
        return $value;
    }
}