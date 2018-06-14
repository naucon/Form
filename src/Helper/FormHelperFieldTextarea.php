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

use Naucon\HtmlBuilder\HtmlBuilder;
use Naucon\HtmlBuilder\HtmlTextarea;

/**
 * Form Helper Field Textarea
 *
 * @package     Form
 * @subpackage  Helper
 * @author      Sven Sanzenbacher
 */
class FormHelperFieldTextarea extends AbstractFormHelperField
{
    /**
     * @return    string                html output
     */
    public function render()
    {
        $htmlElement = new HtmlTextarea($this->getProperty()->getFormName(), $this->getProperty()->getFormValue());
        $htmlElement->setId($this->getProperty()->getFormId());

        foreach ($this->getOptions()->get() as $attributeName => $attributeValue) {
            if ($attributeName == 'value') {
                if (strlen($this->getProperty()->getFormValue()) == 0) {
                    $htmlElement->setContent($value);
                }

            } else {
                // prevent, that already set attributes are overwritten by options
                if (!$htmlElement->hasAttribute($attributeName) || $this->isAttributeInjectable($attributeName)) {
                    $htmlElement->setAttribute($attributeName, $attributeValue);
                }
            }
        }

        if ($this->getProperty()->getEntityContainer()->hasError($this->getProperty()->getName())) {
            $htmlElement->appendAttribute('class', 'nc_form_error', ' ');
        }

        $htmlBuilder = new HtmlBuilder();
        return $htmlBuilder->render($htmlElement) . PHP_EOL;
    }
}