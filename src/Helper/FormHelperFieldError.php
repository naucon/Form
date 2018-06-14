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
use Naucon\HtmlBuilder\HtmlSpan;

/**
 * Form Helper Field Error
 *
 * @package     Form
 * @subpackage  Helper
 * @author      Sven Sanzenbacher
 */
class FormHelperFieldError extends AbstractFormHelperField
{
    /**
     * @return    string                html output
     */
    public function render()
    {
        if ($this->getProperty()->getEntityContainer()->hasError($this->getProperty()->getName())) {
            $formError = $this->getProperty()->getEntityContainer()->getError($this->getProperty()->getName());

            $content = $formError->getMessage();

            $htmlElement = new HtmlSpan($content);
            $htmlElement->setClass('ncFormError');

            foreach ($this->getOptions()->get() as $attributeName => $attributeValue) {
                // prevent, that already set attributes are overwritten by options
                if (!$htmlElement->hasAttribute($attributeName) || $this->isAttributeInjectable($attributeName)) {
                    $htmlElement->setAttribute($attributeName, $attributeValue);
                }
            }

            $htmlBuilder = new HtmlBuilder();
            return $htmlBuilder->render($htmlElement) . PHP_EOL;
        }
        return null;
    }
}