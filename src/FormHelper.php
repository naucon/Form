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

use Naucon\HtmlBuilder as HtmlBuilder;
use Naucon\Form\Helper\AbstractFormHelper;
use Naucon\Form\Helper\FormHelperMap;
use Naucon\Form\Mapper\Property;

/**
 * Form Helper
 *
 * @package     Form
 * @author      Sven Sanzenbacher
 *
 * @example    FormHelperExample.php
 * @example    FormHelperExample2.php
 * @example    FormCollectionHelperExample.php
 */
class FormHelper extends AbstractFormHelper
{
    /**
     * @var    HtmlBuilder\HtmlElementInterface
     */
    protected $htmlElement;

    /**
     * @var    FormHelperMap
     */
    protected $helperMap;


    /**
     * Constructor
     *
     * @param     FormInterface     $form       form instance
     */
    public function __construct(FormInterface $form)
    {
        $htmlElement = new HtmlBuilder\HtmlForm();
        $htmlElement->setId($form->getName());

        if ($form->getConfiguration()->get('csrf_protection')) {
            $tokenHtmlElement = new HtmlBuilder\HtmlInputHidden($form->getConfiguration()->get('csrf_parameter'), $form->getSynchronizerToken());
            $htmlElement->addChildElement($tokenHtmlElement);
        }

        $this->setHtmlElement($htmlElement);

        parent::__construct($form);
    }


    /**
     * @return    HtmlBuilder\HtmlElementInterface
     */
    public function getHtmlElement()
    {
        return $this->htmlElement;
    }

    /**
     * @param     HtmlBuilder\HtmlElementInterface      $htmlElement        html element
     */
    protected function setHtmlElement(HtmlBuilder\HtmlElementInterface $htmlElement)
    {
        $this->htmlElement = $htmlElement;
    }

    /**
     * @return    FormHelperMap
     */
    public function getHelperMap()
    {
        if (is_null($this->helperMap)) {
            $this->helperMap = new FormHelperMap();
        }
        return $this->helperMap;
    }

    /**
     * @param     string        $helperName     form helper name
     * @param     string        $propertyName   form property name
     * @param     array         $options        form options
     * @return    string        html output
     */
    public function formField($helperName, $propertyName, array $options=array())
    {
        $property = $this->current()->getProperty($propertyName);

        if ($property instanceof Property) {
            return $this->getHelperMap()->loadField($property, $helperName, $options);
        }
        return null;
    }

    /**
     * @param     string        $helperName     form helper name
     * @param     string        $propertyName   form property name
     * @param     mixed         $value          form value
     * @param     array         $options        form options
     * @return    string        html output
     */
    public function formChoice($helperName, $propertyName, $value, array $options=array())
    {
        $property = $this->current()->getProperty($propertyName);

        if ($property instanceof Property) {
            return $this->getHelperMap()->loadChoice($property, $helperName, $value, $options);
        }
        return null;
    }

    /**
     * @param     string        $helperName     form helper name
     * @param     string        $content        form tag content
     * @param     array         $options        form options
     * @return    string        html output
     */
    public function formTag($helperName, $content=null, array $options=array())
    {
        return $this->getHelperMap()->loadTag($this->getForm(), $helperName, $content, $options);
    }

    /**
     * @param     string        $helperName     form helper name
     * @param     mixed         $value          form value
     * @param     array         $options        form options
     * @return    string        html output
     */
    public function formOption($helperName, $value, array $options=array())
    {
        return $this->getHelperMap()->loadOption($this->getForm(), $helperName, $value, $options);
    }

    /**
     * @param     string        $method         form method
     * @param     string        $action         form action url
     * @param     string        $enctype        form enctype e.g. "multipart/form-data"
     * @param     array         $options        form options
     * @return    string        html output
     */
    public function formStart($method='post', $action=null, $enctype=null, $options=array())
    {
        $htmlElement = $this->getHtmlElement();
        $htmlElement->setMethod($method);
        $htmlElement->setAction($action);
        $htmlElement->setEnctype($enctype);

        foreach ($options as $key => $value) {
            // prevent, that already set attributes are overwritten by options
            if (!$htmlElement->hasAttribute($key)) {
                $htmlElement->setAttribute($key, $value);
            }
        }

        $htmlBuilder = new HtmlBuilder\HtmlBuilder();
        $render = $htmlBuilder->renderStartTag($htmlElement) . PHP_EOL;
        $render.= $htmlBuilder->renderContent($htmlElement) . PHP_EOL;
        return $render;
    }

    /**
     * @return    string        html output
     */
    public function formEnd()
    {
        $htmlElement = $this->getHtmlElement();

        $htmlBuilder = new HtmlBuilder\HtmlBuilder();
        return $htmlBuilder->renderEndTag($htmlElement) . PHP_EOL;
    }
}