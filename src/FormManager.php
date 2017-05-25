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

use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Naucon\Form\Validator\ValidatorAwareInterface;
use Naucon\Form\Validator\ValidatorAwareTrait;
use Naucon\Form\Validator\Validator;
use Naucon\Form\Security\SynchronizerTokenAwareInterface;
use Naucon\Form\Security\SynchronizerTokenAwareTrait;
use Naucon\Form\Security\SynchronizerTokenNativeSession;
use Naucon\Form\Translator\TranslatorAwareInterface;
use Naucon\Form\Translator\TranslatorAwareTrait;
use Naucon\Form\Translator\Translator;
use Naucon\Form\Translator\TranslatorInterface;

/**
 * Form Manager
 *
 * @package    Form
 * @author     Sven Sanzenbacher
 *
 * @example    FormExample.php
 * @example    FormExample2.php
 * @example    FormCollectionExample.php
 */
class FormManager implements SynchronizerTokenAwareInterface, ValidatorAwareInterface, TranslatorAwareInterface, LoggerAwareInterface
{
    use ValidatorAwareTrait;
    use SynchronizerTokenAwareTrait;
    use TranslatorAwareTrait;
    use LoggerAwareTrait;

    /**
     * @var    \Naucon\Form\Configuration
     */
    protected $configuration;


    /**
     * Constructor
     *
     * @param   array       $options             optional configuration
     */
    public function __construct(array $options=array())
    {
        $this->configuration = new Configuration($options);
    }

    /**
     * @return    TranslatorInterface
     */
    public function getTranslator()
    {
        if (is_null($this->translator)) {
            $this->translator = new Translator($this->configuration);
        }
        return $this->translator;
    }

    /**
     * initialize dependencies
     * like synchronizer token, validator
     */
    protected function initialize()
    {
        if (is_null($this->synchronizerToken)) {
            $this->synchronizerToken = new SynchronizerTokenNativeSession();
        }
        if (is_null($this->validator)) {
            $this->validator = new Validator($this->configuration, $this->getTranslator());
        }
    }

    /**
     * prepare configuration with optional deviating options
     *
     * @param   array       $options            optional deviating configuration
     * @return  Configuration
     */
    protected function prepareConfiguration(array $options=null)
    {
        $configuration = clone $this->configuration;
        if (is_array($options)) {
            $options = array_replace($configuration->all(), $options);
            $configuration = new Configuration($options);
        }
        return $configuration;
    }

    /**
     * create form
     *
     * @param   mixed       $entity             entity or entities
     * @param   string      $name               unique data set name
     * @param   array       $options            optional configuration
     * @return  FormInterface|FormCollectionInterface      created form instance
     */
    public function createForm($entity, $name, array $options=null)
    {
        $configuration = $this->prepareConfiguration($options);

        $this->initialize();

        $form = new Form($entity, $name, $configuration);

        if (!is_null($validator = $this->validator)) {
            $form->setValidator($validator);
        }

        if (!is_null($translator = $this->translator)) {
            $form->setTranslator($translator);
        }

        if (!is_null($synchronizerToken = $this->synchronizerToken)) {
            $form->setSynchronizerToken($synchronizerToken);
        }

        return $form;
    }

    /**
     * create form collection
     *
     * @param     array     $entities           entities
     * @param     string    $name               dataset name
     * @param     array     $options            optional configuration
     * @return    \Naucon\Form\FormCollectionInterface  created form instance
     */
    public function createFormCollection(array $entities, $name, array $options=null)
    {
        $configuration = $this->prepareConfiguration($options);

        $this->initialize();

        $form = new FormCollection($entities, $name, $configuration);
        if (!is_null($validator = $this->validator)) {
            $form->setValidator($validator);
        }

        if (!is_null($translator = $this->translator)) {
            $form->setTranslator($translator);
        }

        if (!is_null($synchronizerToken = $this->synchronizerToken)) {
            $form->setSynchronizerToken($synchronizerToken);
        }

        return $form;
    }
}