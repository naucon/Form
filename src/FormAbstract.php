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

use Naucon\Form\Validator\ValidatorInterface;
use Naucon\Utility\Map;
use Naucon\Utility\Iterator;
use Naucon\Form\Error\FormError;
use Naucon\Form\Exception\InvalidArgumentException;
use Naucon\Form\Mapper\EntityContainerInterface;
use Naucon\Form\Mapper\EntityContainer;
use Naucon\Form\Mapper\PropertyMapper;
use Naucon\Form\Validator\ValidatorAwareTrait;
use Naucon\Form\Validator\ValidatorNull;
use Naucon\Form\Security\SynchronizerTokenAwareTrait;
use Naucon\Form\Security\SynchronizerTokenNull;
use Naucon\Form\Translator\TranslatorNull;
use Naucon\Form\Translator\TranslatorInterface;
use Naucon\Form\Translator\TranslatorAwareTrait;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\NullLogger;

/**
 * Abstract Form
 *
 * @abstract
 * @package    Form
 * @author     Sven Sanzenbacher
 */
abstract class FormAbstract implements FormInterface
{
    use ValidatorAwareTrait;
    use SynchronizerTokenAwareTrait;
    use TranslatorAwareTrait;
    use LoggerAwareTrait;

    /**
     * @var    string
     */
    protected $name = null;

    /**
     * @var    Configuration
     */
    protected $configuration;

    /**
     * @var    \Naucon\Utility\Map
     */
    protected $entityContainerMap;

    /**
     * @var    \Naucon\Utility\Iterator
     */
    protected $entityContainerIterator;

    /**
     * @var    array
     */
    protected $_boundEntities = null;

    /**
     * @var    bool
     */
    protected $bound = false;

    /**
     * @var    bool
     */
    protected $valid = null;



    /**
     * Constructor
     *
     * @param   Configuration       $configuration
     */
    public function __construct(Configuration $configuration)
    {
        $this->configuration = $configuration;
        $this->logger = new NullLogger;
        $this->translator = new TranslatorNull();
        $this->validator = new ValidatorNull();
        $this->synchronizerToken = new SynchronizerTokenNull();
    }

    /**
     * return form name
     *
     * @return    string      form name
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * set form name
     *
     * @param     string        $name          form name
     */
    public function setName($name)
    {
        $this->name = (string)$name;
    }

    /**
     * return configuration
     *
     * @return    Configuration
     */
    public function getConfiguration()
    {
        return $this->configuration;
    }

    /**
     * @return    string        synchronizer token
     */
    public function getSynchronizerToken()
    {
        return $this->synchronizerToken->getToken($this->getName());
    }

    /**
     * @return    TranslatorInterface
     */
    public function getTranslator()
    {
        return $this->translator;
    }

    /**
     * Entity Container map storage
     *
     * entities are wrapped in a entity container. the entity container are registred in a map storage by the entity name.
     *
     * @return    \Naucon\Utility\Map
     */
    public function getEntityContainerMap()
    {
        if (is_null($this->entityContainerMap)) {
            $this->entityContainerMap = new Map();
        }
        return $this->entityContainerMap;
    }

    /**
     * Iterator for entity container map
     *
     * The iterator pulls a copy of the entity container from the map storage.
     * if the map storage changes, for exampe a entity was attached or detached,
     * the iterator have to be reset.
     * @see FormAbstract::resetEntityIterator()
     *
     * @return    \Naucon\Utility\Iterator
     */
    public function getEntityContainerIterator()
    {
        if (is_null($this->entityContainerIterator)) {
            $this->entityContainerIterator = new Iterator($this->getEntityContainerMap()->getAll());
        }
        return $this->entityContainerIterator;
    }

    /**
     * @see FormAbstract::getEntityContainerIterator()
     */
    public function getIterator()
    {
        return $this->getEntityContainerIterator();
    }

    /**
     * return   int     count of registered entity containers
     */
    public function count()
    {
        return count($this->getEntityContainerIterator());
    }

    /**
     * returns the first entity container from map
     *
     * is use if the form contains only contain one and not a collection of entities.
     *
     * @return    EntityContainerInterface
     */
    public function getFirstEntityContainer()
    {
        return $this->getEntityContainerIterator()->current();
    }

    /**
     * reset entity container iterator
     *
     * The iterator pulls a copy of the entity container from the map storage.
     * if the map storage changes, for exampe a entity was attached or detached,
     * the iterator have to be reset.
     * @see FormAbstract::getEntityIterator()
     *
     * @access    protected
     */
    protected function resetEntityIterator()
    {
        $this->_entityIterator = null;
    }

    /**
     * @return    array           bound entities
     */
    public function getBoundEntities()
    {
        return $this->_boundEntities;
    }

    /**
     * @return    object           bound entity
     */
    public function getBoundEntity()
    {
        if (is_array($this->_boundEntities)) {
            return current($this->_boundEntities);
        }
        return null;
    }

    /**
     * @access    protected
     * @param     object        $entity       entity
     */
    protected function addBoundEntity($entity)
    {
        if (!is_object($entity)) {
            throw new InvalidArgumentException('Given entity is not an object.');
        }

        $this->_boundEntities[] = $entity;
    }

    /**
     * return if the submitted form data are bound to the entity
     *
     * @return    bool
     */
    public function isBound()
    {
        return $this->bound;
    }

    /**
     * set bound state
     *
     * @access    protected
     * @param     bool      $bool       bound state
     */
    protected function setBound($bool)
    {
        $this->bound = (bool)$bool;
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
        $this->_boundEntities = array();

        if (!is_null($payload)
            && isset($payload[$this->getName()])
            && $this->validateSynchronizerToken($payload)
        ) {
            $this->logger->debug('start binding form', array('name' => $this->getName(), 'payload' => $payload));

            $propertyMapper = new PropertyMapper();

            /**
             * @var $entityContainer    \Naucon\Form\Mapper\EntityContainerInterface
             */
            if ($this->getEntityContainerMap()->count() > 0) {
                if (!is_null($entityContainer = current($this->getEntityContainerMap()->getAll()))) {
                    $propertyMapper->mapFormToData($entityContainer->getEntity(), $payload[$this->getName()]);
                    $this->addBoundEntity($entityContainer->getEntity());

                    // postbind hook
                    $this->logger->debug('invoke form postbind hook', array('name' => $this->getName(), 'entity' => $entityContainer->getName()));
                    $entityContainer->invokeHook('postbindHook');

                    $bound = true;
                }
            }
        }


        $this->setBound($bound);

        $this->logger->debug('execute post binding form', array('name' => $this->getName()));
        $this->postBind();

        if ($this->isBound()) {
            $this->logger->debug('finished binding form successful', array('name' => $this->getName()));
        } else {
            $this->logger->debug('finished binding form failed', array('name' => $this->getName()));
        }
        return $this->isBound();
    }

    /**
     * post bind processes
     *
     * for example generating a synchronizer token
     */
    protected function postBind()
    {
        if ($this->configuration->get('csrf_protection')) {
            $this->synchronizerToken->generateToken($this->getName());
        }
    }

    /**
     * return if given synchronizer token is valid
     *
     * @access    protected
     * @param     array     $payload      submitted form data
     * @return    bool
     */
    protected function validateSynchronizerToken(array $payload=null)
    {
        if ($this->configuration->get('csrf_protection')) {
            if (!is_null($payload) && isset($payload[$this->configuration->get('csrf_parameter')])) {

                $this->logger->debug('validate synchronizer token', array('name' => $this->getName(), 'token' => $payload[$this->configuration->get('csrf_parameter')]));
                $token = $payload[$this->configuration->get('csrf_parameter')];

                if ($this->synchronizerToken->validate($this->getName(), $token)) {
                    $this->logger->debug('valid synchronizer token given', array('name' => $this->getName(), 'token' => $payload[$this->configuration->get('csrf_parameter')]));
                    return true;
                } else {
                    $this->logger->debug('invalid synchronizer token given', array('name' => $this->getName(), 'token' => $payload[$this->configuration->get('csrf_parameter')]));
                }
            } else {
                $this->logger->debug('no valid synchronizer token given', array('name' => $this->getName()));
            }
            return false;
        } else {
            $this->logger->debug('skip synchronizer token validation', array('name' => $this->getName()));
        }
        return true;
    }

    /**
     * validate bind data
     *
     * @access    protected
     */
    protected function validate()
    {
        if (!$this->validator instanceof ValidatorInterface) {
            return;
        }

        $valid = true;
        if ($this->getEntityContainerMap()->count() > 0) {
            $entityContainers = $this->getEntityContainerMap()->getAll();

            $this->logger->debug('start form entity valiation', array('name' => $this->getName()));

            /**
             * @var $entityContainer    \Naucon\Form\Mapper\EntityContainerInterface
             */
            foreach ($entityContainers as $entityContainer) {
                if ($entityContainer->hasValidate()) {
                    $this->logger->debug('invoke form prevalidator hook', array('name' => $this->getName(), 'entity' => $entityContainer->getName()));
                    // prevalidation hook
                    $entityContainer->invokeHook('prevalidatorHook');

                    $violations = $this->validator->validate($entityContainer->getEntity(), null, $this->configuration->get('validation_groups'));
                    if (is_array($violations) && count($violations)) {
                        // TODO set FormValidations to $entityContainer
                        /**
                         * @var \Naucon\Form\Validator\ViolationInterface $violation
                         */
                        $violationsDump = array();
                        foreach ($violations as $violation) {
                            $violationsDump[$violation->getName()] = $violation->getMessage();
                            $entityContainer->setError($violation->getName(), new FormError($violation->getName(), $violation->getMessage()));
                        }
                        $valid = false;

                        $this->logger->debug('form entity violations', array('name' => $this->getName(), 'entity' => $entityContainer->getName(), 'violations' => $violationsDump));
                    }

                    // postvalidation hook
                    $this->logger->debug('invoke form postvalidator hook', array('name' => $this->getName(), 'entity' => $entityContainer->getName()));
                    $entityContainer->invokeHook('postvalidatorHook');

                    if ($entityContainer->hasErrors()) {
                        $valid = false;
                    }
                }
            }
        }

        if ($valid) {
            $this->logger->debug('finished form entity valiation successful', array('name' => $this->getName()));
        } else {
            $this->logger->debug('finished form entity valiation failed', array('name' => $this->getName()));
        }
        $this->setValid($valid);
    }

    /**
     * returns if the submitted form data are valid
     *
     * @return    bool
     */
    public function isValid()
    {
        if (is_null($this->valid)) {
            $this->validate();
        }
        return $this->valid;
    }

    /**
     * set validation state
     *
     * @access    protected
     * @param     bool      $bool       validation state
     */
    protected function setValid($bool)
    {
        $this->valid = (bool)$bool;
    }

    /**
     * returns a array of form errors
     *
     * @return      array
     */
    abstract public function getErrors();

    /**
     * add form entity container to entity map
     *
     * @access    protected
     * @param     EntityContainerInterface       $entityContainer
     */
    protected function attachEntityContainer(EntityContainerInterface $entityContainer)
    {
        $this->getEntityContainerMap()->set($entityContainer->getName(), $entityContainer);

        // reset entity cached iterator
        $this->resetEntityIterator();
    }

    /**
     * add a array of form entities to entity map
     *
     * @access    protected
     * @param     array     $entities           form entities
     */
    protected function attachEntityContainers(array $entities)
    {
        $this->getEntityContainerMap()->setAll($entities);

        // reset entity cached iterator
        $this->resetEntityIterator();
    }

    /**
     * remove a form entity from entity map
     *
     * @access    protected
     * @param     string    $name       form entity name
     */
    protected function detachEntityContainer($name)
    {
        $this->getEntityContainerMap()->remove($name);

        $this->resetEntityIterator();
    }

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
    public function attachEntity($entity, $name=null)
    {
        $entityContainer = new EntityContainer($this, $entity, $name);
        $this->attachEntityContainer($entityContainer);
    }

    /**
     * add array of entities
     *
     * the given entities will be wrapped in a entity containers.
     * this entity containers are registred in a map storage by a given entity name.
     * the entity names are the keys of the given array.
     *
     * @param     array         $entities       array of entities, the key of the array will be used as entity name
     */
    public function attachEntities(array $entities)
    {
        foreach ($entities as $name => $entitie) {
            $this->attachEntity($entitie, $name);
        }
    }

    /**
     * remove entity by entity name
     *
     * remove a entity with its entity container from the map storage by a given entity name.
     *
     * @see       FormAbstract::detachEntityContainer()
     * @param     string        $name       entity name
     */
    public function detachEntity($name)
    {
        $this->detachEntityContainer($name);
    }
}
