<?php
/*
 * Copyright 2008 Sven Sanzenbacher
 *
 * This file is part of the naucon package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Naucon\Form\Helper;

use Naucon\Form\FormCollectionInterface;
use Naucon\Form\FormInterface;
use Naucon\Form\Helper\Exception\InvalidArgumentException;
use Naucon\Form\Mapper\Property;
use Naucon\Utility\Map;

/**
 * Form Helper Map
 *
 * @package     Form
 * @subpackage  Helper
 * @author      Sven Sanzenbacher
 */
class FormHelperMap
{
    /**
     * @var     Map
     */
    protected $helperMap = null;



    /**
     * Constructor
     */
    public function __construct()
    {
        $this->attachDefaultHelper();
    }


    /**
     * attach default form helper
     */
    public function attachDefaultHelper()
    {
        $this->attachHelper('checkbox', new FormHelperChoiceCheckbox());
        $this->attachHelper('radio',    new FormHelperChoiceRadio());
        $this->attachHelper('select',   new FormHelperChoiceSelect());
        $this->attachHelper('error',    new FormHelperFieldError());
        $this->attachHelper('hidden',   new FormHelperFieldHidden());
        $this->attachHelper('id',       new FormHelperFieldId());
        $this->attachHelper('label',    new FormHelperFieldLabel());
        $this->attachHelper('name',     new FormHelperFieldName());
        $this->attachHelper('password', new FormHelperFieldPassword());
        $this->attachHelper('text',     new FormHelperFieldText());
        $this->attachHelper('textarea', new FormHelperFieldTextarea());
        $this->attachHelper('value',    new FormHelperFieldValue());
        $this->attachHelper('radio',    new FormHelperOptionRadio());
        $this->attachHelper('errors',   new FormHelperTagErrors());
        $this->attachHelper('reset',    new FormHelperTagReset());
        $this->attachHelper('submit',   new FormHelperTagSubmit());
    }


    /**
     * load and render form help field
     *
     * @param Property $property   property instance
     * @param string   $helperName form helper name
     * @param array    $options    form helper options
     *
     * @return    string        rendered html
     *
     * @throws  InvalidArgumentException
     * @throws \Naucon\Utility\Exception\MapException
     */
    public function loadField(Property $property, $helperName, array $options = [])
    {
        if ($this->getHelper('field')->hasKey($helperName)) {
            /**
             * @var $formHelper FormHelperFieldInterface
             */
            $formHelper = $this->getHelper('field')->get($helperName);
            $tmpFormHelper = clone $formHelper;
            $tmpFormHelper->setProperty($property);
            $tmpFormHelper->setOptions($options);
            return $tmpFormHelper->render();
        } else {
            throw new InvalidArgumentException('unkown form helper ' . $helperName);
        }
    }

    /**
     * load and render form help choice
     *
     * @param     Property      $property           property instance
     * @param     string            $helperName         form helper choice name
     * @param     mixed             $choiceValue        form helper choice value or values
     * @param     array             $options            form helper options
     * @return    string        rendered html
     *
     * @throws  InvalidArgumentException
     */
    public function loadChoice(Property $property, $helperName, $choiceValue, array $options = [])
    {
        if ($this->getHelper('choice')->hasKey($helperName)) {
            /**
             * @var $formHelper FormHelperChoiceInterface
             */
            $formHelper = $this->getHelper('choice')->get($helperName);
            $tmpFormHelper = clone $formHelper;
            $tmpFormHelper->setProperty($property);
            if (is_array($choiceValue)) {
                $tmpFormHelper->setChoices($choiceValue);
            } else {
                $tmpFormHelper->setChoice($choiceValue);
            }
            $tmpFormHelper->setOptions($options);
            return $tmpFormHelper->render();
        } else {
            throw new InvalidArgumentException('unkown form helper ' . $helperName);
        }
    }

    /**
     * load and render form help tag
     *
     * @param     FormInterface     $form           form instance
     * @param     string            $helperName     form helper name
     * @param     string            $content        form helper content
     * @param     array             $options        form helper options
     * @return    string        rendered html
     *
     * @throws  InvalidArgumentException
     */
    public function loadTag(FormInterface $form, $helperName, $content=null, array $options = [])
    {
        if ($this->getHelper('tag')->hasKey($helperName)) {
            /**
             * @var $formHelper FormHelperTagInterface
             */
            $formHelper = $this->getHelper('tag')->get($helperName);
            $tmpFormHelper = clone $formHelper;
            $tmpFormHelper->setForm($form);
            $tmpFormHelper->setContent($content);
            $tmpFormHelper->setOptions($options);
            return $tmpFormHelper->render();
        } else {
            throw new InvalidArgumentException('unkown form helper ' . $helperName);
        }
    }

    /**
     * load and render form help option
     *
     * @param     FormInterface     $form               form collection instance
     * @param     string            $helperName         form helper name
     * @param     string            $optionValue        form helper option value or values
     * @param     array             $options            form helper options
     * @return    string        rendered html
     *
     * @throws  InvalidArgumentException
     */
    public function loadOption(FormInterface $form, $helperName, $optionValue, array $options = [])
    {
        if (!$form instanceof FormCollectionInterface) {
            throw new InvalidArgumentException(
                sprintf('loadOption requires a instance of Type "%s", "%s" was given.',
                    FormCollectionInterface::class,
                    get_class($form))
            );
        }

        if ($this->getHelper('option')->hasKey($helperName)) {
            /**
             * @var $formHelper FormHelperOptionInterface
             */
            $formHelper = $this->getHelper('option')->get($helperName);
            $tmpFormHelper = clone $formHelper;
            $tmpFormHelper->setForm($form);
            if (is_array($optionValue)) {
                $tmpFormHelper->setChoices($optionValue);
            } else {
                $tmpFormHelper->setChoice($optionValue);
            }
            $tmpFormHelper->setOptions($options);
            return $tmpFormHelper->render();
        } else {
            throw new InvalidArgumentException('unkown form helper ' . $helperName);
        }
    }


    /**
     * @return      \Naucon\Utility\Map
     */
    protected function getHelperMap()
    {
        if (is_null($this->helperMap)) {
            $this->helperMap = new Map();
        }
        return $this->helperMap;
    }

    /**
     * @param       string      $helperType     helper type eg. tag, option, field, choise
     * @return      bool
     *
     * @throws InvalidArgumentException
     */
    public function hasName($helperType)
    {
        if ($this->isValidKey($helperType)) {
            if ($this->getHelperMap()->hasKey($helperType)) {
                return true;
            }
        } else {
            throw new InvalidArgumentException('Invalid form helper type given');
        }
        return false;
    }

    /**
     * return registered form helper
     *
     * @param       string      $helperType         helper type eg. tag, option, field, choise
     * @return      Map
     *
     * @throws InvalidArgumentException
     */
    public function getHelper($helperType)
    {
        if ($this->isValidKey($helperType)) {
            return $this->getHelperMap()->get($helperType);
        } else {
            throw new InvalidArgumentException('Invalid form helper type given');
        }
    }

    /**
     * return count of registered form helper
     *
     * @param       string      $helperType         helper type eg. tag, option, field, choise
     * @return      int
     *
     * @throws  InvalidArgumentException
     */
    public function getHelperCount($helperType)
    {
        if ($this->isValidKey($helperType)) {
            if (!is_null($formHelperMap = $this->getHelperMap()->get($helperType))) {
                return count($formHelperMap);
            }
            return 0;
        } else {
            throw new InvalidArgumentException('Invalid form helper type given');
        }
    }

    /**
     * return if helper are registered
     *
     * @param       string      $helperType         helper type eg. tag, option, field, choise
     * @return      bool
     *
     * @throws  InvalidArgumentException
     */
    public function hasHelper($helperType)
    {
        if ($this->isValidKey($helperType)) {
            if (!is_null($formHelperMap = $this->getHelperMap()->get($helperType))
                && count($formHelperMap) > 0
            ) {
                return true;
            }
        } else {
            throw new InvalidArgumentException('Invalid form helper type given');
        }
        return false;
    }

    /**
     * @param       string      $key        key name
     * @return      bool
     */
    protected function isValidKey($key)
    {
        if (is_scalar($key)
            && strlen($key) > 0
        ) {
            return true;
        }
        return false;
    }

    /**
     * @param       FormHelperInterface     $helper     form helper instance
     * @return      string
     */
    protected function resolveHelperType(FormHelperInterface $helper)
    {
        $formHelperType = null;
        if ($helper instanceof FormHelperChoiceInterface) {
            $formHelperType = 'choice';
        }
        elseif ($helper instanceof FormHelperFieldInterface) {
            $formHelperType = 'field';
        }
        elseif ($helper instanceof FormHelperOptionInterface) {
            $formHelperType = 'option';
        }
        elseif ($helper instanceof FormHelperTagInterface) {
            $formHelperType = 'tag';
        }
        return $formHelperType;
    }

    /**
     * add form helper
     *
     * @param       string                  $helperName         form helper name
     * @param       FormHelperInterface     $formHelper         form helper instance
     * @return      FormHelperMap       form helper map for fluent interface
     *
     * @throws InvalidArgumentException
     */
    public function attachHelper($helperName, FormHelperInterface $formHelper)
    {
        if ($this->isValidKey($helperName)) {
            if (is_null($formHelperType = $this->resolveHelperType($formHelper))) {
                throw new InvalidArgumentException('Unkown form helper instance given');
            }

            if ($this->hasHelper($formHelperType)) {
                $this->getHelperMap()->get($formHelperType)->set($helperName, $formHelper);
            } else {
                $helperMap = new Map();
                $helperMap->set($helperName, $formHelper);
                $this->getHelperMap()->set($formHelperType, $helperMap);
            }
        } else {
            throw new InvalidArgumentException('Invalid form helper name given');
        }
        return $this;
    }

    /**
     * remove form helper
     *
     * @param       string                  $helperName     form helper name
     * @param       FormHelperInterface     $helper         form helper instance
     * @return      FormHelperMap       form helper map for fluent interface
     *
     * @throws      InvalidArgumentException
     */
    public function detachHelper($helperName, FormHelperInterface $helper)
    {
        if ($this->isValidKey($helperName)) {
            if (is_null($formHelperType = $this->resolveHelperType($helper))) {
                throw new InvalidArgumentException('Unkown form helper instance given');
            }

            if ($this->hasHelper($formHelperType)) {
                $this->getHelperMap()->get($formHelperType)->remove($helperName);
            }
        } else {
            throw new InvalidArgumentException('Not able to detach helper because invalid helper name given');
        }
        return $this;
    }
}
