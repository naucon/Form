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

use Naucon\Form\Exception\InvalidArgumentException;
use Naucon\Form\Mapper\EntityContainerInterface;

/**
 * Form Class
 *
 * @package    Form
 * @author     Sven Sanzenbacher
 */
class Form extends FormAbstract
{
    /**
     * Constructor
     *
     * @param   object|array            $entity          entity or entities
     * @param   string                  $name            unique form name
     * @param   Configuration           $configuration
     */
    public function __construct($entity, $name, Configuration $configuration)
    {
        if (empty($name)) {
            throw new InvalidArgumentException('Given form name can not be empty.');
        }
        $this->setName($name);

        if (is_array($entity)) {
            throw new InvalidArgumentException('Given entity is a array please use FormCollection instead.');
        } elseif (is_object($entity)) {
            $this->attachEntity($entity);
        } else {
            throw new InvalidArgumentException('Form has no valid entity.');
        }

        parent::__construct($configuration);
    }

    /**
     * returns a array of form errors
     *
     * @return      array
     */
    public function getErrors()
    {
        $errors = [];

        /**
         * @var EntityContainerInterface $entityContainer
         */
        if (!is_null($entityContainer = current($this->getEntityContainerMap()->getAll()))
            && $entityContainer->hasErrors()
        ) {
            $errors = $entityContainer->getErrors();
        }

        return $errors;
    }
}
