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

use Naucon\Utility\Map;
use Naucon\Form\FormInterface;
use Naucon\Form\FormHook;
use Naucon\Form\Exception\InvalidArgumentException;
use Naucon\Form\Validator\ViolationMap;
use Naucon\Form\Validator\ViolationMapInterface;
use Naucon\Form\Error\FormErrorInterface;

/**
 * Entity Container
 *
 * @package     Form
 * @subpackage  Mapper
 * @author      Sven Sanzenbacher
 */
class EntityContainer implements EntityContainerInterface
{
    /**
     * @var    FormInterface        bind form to entity container
     */
    protected $form;

    /**
     * @var    string       entity name
     */
    protected $name;

    /**
     * @var    bool     if the entity shell be validated
     */
    protected $validate = true;

    /**
     * @var    object       entity
     */
    protected $entity;

    /**
     * @var    \Naucon\Utility\Map
     */
    protected $propertyMap;

    /**
     * @var    \Naucon\Utility\Map
     */
    protected $errorMap;

    /**
     * @var    ViolationMapInterface
     */
    protected $violationMap;


    /**
     * Constructor
     *
     * @param    FormInterface      $form           form instance
     * @param    object             $entity         entity
     * @param    string             $name           optional entity name
     */
    public function __construct(FormInterface $form, $entity, $name=null)
    {
        $this->setForm($form);

        if (!is_object($entity)) {
            throw new InvalidArgumentException('Form entity has no valid entity');
        }
        $this->setEntity($entity);

        if (is_null($name)) {
            $this->setName(0);
        } else {
            $this->setName($name);
        }
    }

    /**
     * return to what form the entity is bind
     *
     * @return    FormInterface     form instance
     */
    public function getForm()
    {
        return $this->form;
    }

    /**
     * bind form to entity container
     *
     * @param     FormInterface     $form       form instance
     */
    protected function setForm(FormInterface $form)
    {
        $this->form = $form;
    }

    /**
     * return entity name
     *
     * @return    string        entity name
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * set entity name
     *
     * @param     string        $name        entity name
     */
    protected function setName($name)
    {
        $this->name = $name;
    }

    /**
     * return if the entity shell be validated
     *
     * @return    bool      true = entity will be validated, false = skip validation
     */
    public function hasValidate()
    {
        return $this->validate;
    }

    /**
     * define if the entity shell be validated
     *
     * @param     bool      $bool       true = entity will be validated, false = skip validation
     */
    public function setValidate($bool)
    {
        $this->validate = (bool)$bool;
    }

    /**
     * return entity
     *
     * @return    object        entity
     */
    public function getEntity()
    {
        return $this->entity;
    }

    /**
     * set entity
     *
     * @param     object        $entity         entity
     */
    protected function setEntity($entity)
    {
        $this->entity = $entity;
    }

    /**
     * @param     string        $name            property name
     * @return    string        form name
     */
    public function getFormName($name)
    {
        return $this->getProperty($name)->getFormName();
    }

    /**
     * @param     string        $name       property name
     * @return    string        form value
     */
    public function getFormValue($name)
    {
        return $this->getProperty($name)->getFormValue();
    }

    /**
     * @return    Map
     */
    public function getPropertyMap()
    {
        if (is_null($this->propertyMap)) {
            $this->propertyMap = new Map();

            $this->prepareProperties();
        }
        return $this->propertyMap;
    }

    /**
     * prepare properties
     */
    protected function prepareProperties()
    {
        // leave the property creation up to usage instead of precreation
        //$formMapper = new PropertyMapper();
        //$formMapper->attachProperty($this);
    }

    /**
     * get form property
     *
     * @param     string    $propertyName       form property name
     * @return    Property
     */
    public function getProperty($propertyName)
    {
        if ($this->getPropertyMap()->hasKey($propertyName)) {
            return $this->getPropertyMap()->get($propertyName);
        }

        return $this->attachProperty(new Property($this, $propertyName));
    }

    /**
     * add form property
     *
     * @param     Property      $property
     * @return    Property
     */
    public function attachProperty(Property $property)
    {
        return $this->getPropertyMap()->set($property->getName(), $property);
    }

    /**
     * remove form property
     *
     * @param     string        $name       property name
     */
    public function detachProperty($name)
    {
        $this->getPropertyMap()->remove($name);
    }

    /**
     * return violations
     *
     * @access    protected
     * @return    ViolationMapInterface
     */
    protected function getViolationMap()
    {
        if (is_null($this->violationMap)) {
            $this->violationMap = new ViolationMap();
        }
        return $this->violationMap;
    }

    /**
     * return violations
     *
     * @return    ViolationMap
     * @see EntityContainer::getViolationMap()
     */
    public function getViolations()
    {
        return $this->getViolationMap();
    }

    /**
     * return map of entity errors
     *
     * @return    Map
     */
    protected function getErrorMap()
    {
        if (is_null($this->errorMap)) {
            $this->errorMap = new Map();
        }
        return $this->errorMap;
    }

    /**
     * return entity errors
     *
     * @return    array             entity errors
     */
    public function getErrors()
    {
        return $this->getErrorMap()->getAll();
    }

    /**
     * has entity errors
     *
     * @return    bool
     */
    public function hasErrors()
    {
        if ($this->getErrorMap()->count() > 0) {
            return true;
        }
        return false;
    }

    /**
     * return entity error of a given key
     *
     * @param     string        $key        key
     * @return    FormErrorInterface
     */
    public function getError($key)
    {
        return $this->getErrorMap()->get($key);
    }

    /**
     * has entity error of given key
     *
     * @param     string        $key        key
     * @return    bool
     */
    public function hasError($key)
    {
        return $this->getErrorMap()->hasKey($key);
    }

    /**
     * set entity error for a given key
     *
     * @param     string        $key        key
     * @param     FormErrorInterface    $formError      form error instance
     */
    public function setError($key, FormErrorInterface $formError)
    {
        $this->getErrorMap()->set($key, $formError);
    }

    /**
     * invoke hook
     *
     * calls a hook method of the entity
     *
     * @param     string        $methodName            hook method name
     */
    public function invokeHook($methodName)
    {
        if (method_exists($this->getEntity(), $methodName)) {
            $this->getEntity()->$methodName(new FormHook($this));
        }
    }
}
