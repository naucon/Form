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

use Naucon\Form\Validator\ValidatorAwareInterface;
use Naucon\Form\Security\SynchronizerTokenAwareInterface;
use Naucon\Form\Translator\TranslatorAwareInterface;
use Psr\Log\LoggerAwareInterface;

/**
 * Form Interface
 *
 * @package    Form
 * @author     Sven Sanzenbacher
 */
interface FormInterface extends \IteratorAggregate, \Countable, SynchronizerTokenAwareInterface, ValidatorAwareInterface, TranslatorAwareInterface, LoggerAwareInterface
{
    /**
     * return form name
     *
     * @return     string      form name
     */
    public function getName();

    /**
     * set form name
     *
     * @param   string          form name
     */
    public function setName($name);

    /**
     * return configuration
     *
     * @return    \Naucon\Form\Configuration
     */
    public function getConfiguration();

    /**
     * @return    string        synchronizer token
     */
    public function getSynchronizerToken();

    /**
     * @return      \Naucon\Form\Translator\TranslatorInterface
     */
    public function getTranslator();

    /**
     * @return    array           bound entities
     */
    public function getBoundEntities();

    /**
     * @return    object           bound entity
     */
    public function getBoundEntity();

    /**
     * bind a array of submitted form data eg. $_POST
     *
     * @param     array     $payload      submitted form data
     * @return    bool
     */
    public function bind(array $payload=null);

    /**
     * returns if the submitted form data are valid
     *
     * @return    bool
     */
    public function isBound();

    /**
     * returns if the submitted form data are valid
     *
     * @return    bool
     */
    public function isValid();

    /**
     * returns a array of form errors
     *
     * @return      array
     */
    public function getErrors();

    /**
     * Entity Container map storage
     *
     * entities are wrapped in a entity container. the entity container are registred in a map storage by the entity name.
     *
     * @return    \Naucon\Utility\Map
     */
    public function getEntityContainerMap();

    /**
     * Iterator for entity containers
     *
     * @return    \Naucon\Utility\Iterator
     */
    public function getEntityContainerIterator();

    /**
     * add entity
     *
     * given entity will be wrapped in a entity container.
     * the entity container is registred in a map storage by the given entity name.
     * If the entity name is not give the entity will be registred with the entity name 0.
     *
     * @param     object        $entity             entity
     * @param     string        $name               entity name
     */
    public function attachEntity($entity, $name=null);

    /**
     * add array of entities
     *
     * the given entities will be wrapped in a entity containers.
     * this entity containers are registred in a map storage by a given entity name.
     * the entity names are the keys of the given array.
     *
     * @param     array         $entities       array of entities, the key of the array will be used as entity name
     */
    public function attachEntities(array $entities);

    /**
     * remove entity by entity name
     *
     * remove a entity with its entity container from the map storage by a given entity name.
     *
     * @param     string        $name       entity name
     */
    public function detachEntity($name);
}