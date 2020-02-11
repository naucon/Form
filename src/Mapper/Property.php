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

use Naucon\Form\FormCollectionInterface;
use Naucon\Form\FormInterface;

/**
 * Property
 *
 * The form properties are representations of the form fields.
 * they are created from the data entity by the form builder.
 *
 * @package    Form
 * @author     Sven Sanzenbacher
 */
class Property
{
    /**
     * @var    EntityContainerInterface     bind entity container to property
     */
    protected $entityContainer = null;

    /**
     * @var    string       property name
     */
    protected $name = null;


    /**
     * Constructor
     *
     * @param    EntityContainerInterface   $entityContainer        entity container instance
     * @param    string     $name       property name
     */
    public function __construct(EntityContainerInterface $entityContainer, $name)
    {
        $this->setEntityContainer($entityContainer);
        $this->setName($name);
    }


    /**
     * return to what entity container the property is bind
     *
     * @return    EntityContainerInterface      entity container instance
     */
    public function getEntityContainer()
    {
        return $this->entityContainer;
    }

    /**
     * bind entity container to property
     *
     * @param     EntityContainerInterface      $entityContainer        entity container instance
     */
    protected function setEntityContainer(EntityContainerInterface $entityContainer)
    {
        $this->entityContainer = $entityContainer;
    }

    /**
     * access form instance through entity container
     *
     * @see FromProperty::getEntityContainer()::getForm()
     *
     * @return    FormInterface     form instance
     */
    public function getForm()
    {
        return $this->getEntityContainer()->getForm();
    }

    /**
     * get property name
     *
     * @return    string            property name
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * set property name
     *
     * @param     string        $name       property name
     */
    protected function setName($name)
    {
        $this->name = $name;
    }

    /**
     * return prepared property id for html attribute id
     *
     * @return    string            property id
     */
    public function getFormId()
    {
        // allowed are a-zA-Z0-9-_:.
        // no spaces

        $form = $this->getForm();
        $formId = $form->getName();

        if ($form instanceof FormCollectionInterface) {
            $formId .= '_' . $this->getEntityContainer()->getName();
        }

        $formId .= '_' . $this->getName();
        return $formId;
    }

    /**
     * return prepared property name for html attribute name
     *
     * @return    string            property name
     */
    public function getFormName()
    {
        $form = $this->getForm();
        $formName = $form->getName();

        if ($form instanceof FormCollectionInterface) {
            $formName .= '[' . $this->getEntityContainer()->getName() . ']';
        }

        $formName .= '[' . $this->getName() . ']';
        return $formName;
    }

    /**
     * return property value
     *
     * @return    string            property form value
     */
    public function getFormValue()
    {
        $mapper = new PropertyMapper();
        return $mapper->mapDataToForm($this->getEntityContainer()->getEntity(), $this->getName());
    }

    /**
     * return prepared property label
     *
     * @param     string    $domain     optional translation domain
     * @return    string            property name
     */
    public function getFormLabel($domain=null)
    {
        $form = $this->getForm();
        $translationDomain = !is_null($domain) ? $domain : 'forms';
        $translator = $form->getTranslator();

        $id = $form->getName();
        if ($form instanceof FormCollectionInterface) {
            if (!is_int($this->getEntityContainer()->getName())) {
                $id .= '.' . $this->getEntityContainer()->getName();
            }
        }
        $id .= '.' . $this->getName();

        return $translator->trans($id, [], $translationDomain);
    }
}
