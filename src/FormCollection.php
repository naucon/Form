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
use Naucon\Form\Mapper\PropertyMapper;
use Naucon\Form\Mapper\EntityContainerInterface;
use Naucon\Utility\Iterator;

/**
 * Form Collection
 *
 * @package    Form
 * @author     Sven Sanzenbacher
 */
class FormCollection extends FormAbstract implements FormCollectionInterface
{
    /**
     * @var   string
     */
    protected $formOptionKey = 'ncform_option';

    /**
     * @var   array
     */
    protected $_formOptionValues = [];

    /**
     * @var   array
     */
    protected $_formOptionDefaultValues = null;


    /**
     * Constructor
     *
     * @param  array         $entities           entitis
     * @param  string        $name               dataset name
     * @param  Configuration       $configuration
     */
    public function __construct(array $entities, $name, Configuration $configuration)
    {
        if (empty($name)) {
            throw new InvalidArgumentException('Given form name can not be empty.');
        }
        $this->setName($name);

        if (is_array($entities)) {
            if (count($entities) > 0) {
                $this->attachEntities($entities);
            }
        } else {
            throw new InvalidArgumentException('Form has no valid entity or entities.');
        }

        parent::__construct($configuration);
    }

    /**
     * return form option key
     *
     * @return    string                    form option key
     */
    public function getFormOptionKey()
    {
        return $this->formOptionKey;
    }

    /**
     * set form option key
     *
     * @param     string        $type       form option key
     */
    public function setFormOptionKey($type)
    {
        $this->formOptionKey = (string)$type;
    }

    /**
     * return form option default value
     *
     * @return    array                     form option default values
     */
    public function getFormOptionDefaultValues()
    {
        return $this->_formOptionDefaultValues;
    }

    /**
     * set form option default value
     *
     * @param     array     $defaultValues      form option default values
     */
    public function setFormOptionDefaultValues(array $defaultValues)
    {
        $this->_formOptionDefaultValues = $defaultValues;
    }

    /**
     * add form option default value
     *
     * @param     string    $formEntityName     form entity name as form option default value
     */
    public function addFormOptionDefaultValues($formEntityName)
    {
        $this->_formOptionDefaultValues[] = $formEntityName;
    }

    /**
     * return form options
     *
     * @param     array     $payload      submitted form data
     * @return    array
     */
    protected function buildFormOptions(array $payload=null)
    {
        $formOptions = null;
        switch ($this->configuration->get('collection_type')) {
            case self::COLLECTION_TYPE_ONE:
            case self::COLLECTION_TYPE_MANY:
                $formOptions = [];
                if (!is_null($payload)
                    && isset($payload[$this->getName()])
                ) {
                    if (isset($payload[$this->getName()][$this->getFormOptionKey()])) {
                        if (is_array($payload[$this->getName()][$this->getFormOptionKey()])) {
                            $formOptions = $payload[$this->getName()][$this->getFormOptionKey()];
                        } else {
                            $formOptions = [$payload[$this->getName()][$this->getFormOptionKey()]];
                        }
                        $this->logger->debug(
                            'form with option "{value}"',
                            [
                                'name' => $this->getName(),
                                'value' => $payload[$this->getName()][$this->getFormOptionKey()]
                            ]
                        );
                    } else {
                        $this->logger->debug(
                            'missing form option',
                            ['name' => $this->getName(), 'payload' => $payload]
                        );
                        // TODO set error
                    }
                }
                break;
        }

        return $formOptions;
    }

    /**
     * bind a array of submitted form data eg. $_POST
     *
     * @param     array     $payload      submitted form data
     * @return    bool
     */
    public function bind(array $payload=null)
    {
        $bound = false;
        $this->_boundEntities = [];

        if (!is_null($payload)
            && isset($payload[$this->getName()])
            && $this->validateSynchronizerToken($payload)
        ) {
            $this->logger->debug(
                'start binding form',
                ['name' => $this->getName(), 'payload' => $payload]
            );

            $propertyMapper = new PropertyMapper();

            $formOptions = $this->buildFormOptions($payload);

            if ($this->getEntityContainerMap()->count() > 0) {
                $entityContainers = $this->getEntityContainerMap()->getAll();

                /**
                 * @var $entityContainer    \Naucon\Form\Mapper\EntityContainerInterface
                 */
                foreach ($entityContainers as $entityContainer) {
                    $name = $entityContainer->getName();

                    if (is_null($formOptions) || in_array($name, $formOptions)) {
                        $this->_formOptionValues[] = $name;
                        if (isset($payload[$this->getName()][$name])) {
                            $formData = $payload[$this->getName()][$name];

                            $propertyMapper->mapFormToData($entityContainer->getEntity(), $formData);
                            $this->addBoundEntity($entityContainer->getEntity());

                            // postbind hook
                            $this->logger->debug(
                                'invoke form postbind hook',
                                [
                                    'name' => $this->getName(),
                                    'entity' => $entityContainer->getName()
                                ]
                            );
                            $entityContainer->invokeHook('postbindHook');

                        }
                    } elseif (!is_null($formOptions) && !in_array($name, $formOptions)) {
                        $entityContainer->setValidate(false);
                    }
                }
            }

            switch($this->configuration->get('collection_type'))
            {
                case self::COLLECTION_TYPE_ONE:
                    if (count($this->getBoundEntities())==1) {
                        $bound = true;
                    }
                    break;
                case self::COLLECTION_TYPE_MANY:
                    if (count($this->getBoundEntities()) == count($this->_formOptionValues)) {
                        $bound = true;
                    }
                    break;
                case self::COLLECTION_TYPE_ALL:
                    if ($this->getEntityContainerMap()->count() == count($this->getBoundEntities())) {
                        $bound = true;
                    }
                    break;
                case self::COLLECTION_TYPE_ANY:
                    if ($this->getEntityContainerMap()->count() > 0) {
                        $bound = true;
                    }
                    break;
            }

        }

        $this->setBound($bound);

        $this->logger->debug('execute post binding form', ['name' => $this->getName()]);
        $this->postBind();

        if ($this->isBound()) {
            $this->logger->debug('finished binding form successful', ['name' => $this->getName()]);
        } else {
            $this->logger->debug('finished binding form failed', ['name' => $this->getName()]);
        }
        return $this->isBound();
    }

    /**
     * @return    string                    form name of form option
     */
    public function getFormOptionName()
    {
        $formName = $this->getName();
        $formName .= '[' . $this->getFormOptionKey() . ']';

        if ($this->configuration->get('collection_type') == self::COLLECTION_TYPE_MANY) {
            $formName .= '[]';
        }

        return $formName;
    }

    /**
     * @param     string        $name       form name
     * @return    bool
     */
    public function isFormOptionSelected($name)
    {
        $selected = false;
        if (!is_null($this->_formOptionValues)) {
            if (count($this->_formOptionValues) > 0) {
                if (in_array($name, $this->_formOptionValues)) {
                    $selected = true;
                }
            } else {
                // set default value
                if ($this->configuration->get('collection_type') == self::COLLECTION_TYPE_ONE) {
                    if (is_null($this->getFormOptionDefaultValues())) {
                        // create new iterator instance to avoid problems with iterator pointer
                        $entityIterator = new Iterator($this->getEntityContainerMap()->getAll());
                        $entityContainer = $entityIterator->current();
                        if ($entityContainer->getName() == $name) {
                            $selected = true;
                        }
                    } elseif (count($this->getFormOptionDefaultValues()) > 0) {
                        if (in_array($name, $this->getFormOptionDefaultValues())) {
                            $selected = true;
                        }
                    }
                }
            }
        }
        return $selected;
    }

    /**
     * returns a array of form errors
     *
     * @return      array
     */
    public function getErrors()
    {
        $errors = [];

        $entityContainers = $this->getEntityContainerMap()->getAll();

        /**
         * @var EntityContainerInterface $entityContainer
         */
        foreach ($entityContainers as $entityContainer) {
            if ($entityContainer->hasErrors()) {
                $errors[$entityContainer->getName()] = $entityContainer->getErrors();
            }
        }

        return $errors;
    }
}
