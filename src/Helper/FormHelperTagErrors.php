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

use Naucon\Form\Error\FormErrorsDecoratorStringify;
use Naucon\HtmlBuilder\HtmlBuilder;
use Naucon\HtmlBuilder\HtmlDiv;

/**
 * Form Helper Tag Errors
 *
 * @package     Form
 * @subpackage  Helper
 * @author      Sven Sanzenbacher
 */
class FormHelperTagErrors extends AbstractFormHelperTag
{
    /**
     * @return    string                html output
     */
    public function render()
    {
        $formErrors = new FormErrorsDecoratorStringify($this->getForm());
        $errors = $formErrors->getErrors();

        $output = null;
        if (count($errors) > 0) {
            foreach ($errors as $error) {
                $output.= $this->_renderError($error);
            }
        }
        return $output;
    }

    /**
     * @access    protected
     * @param     string        $error      error string
     * @return    string        html output
     */
    protected function _renderError($error)
    {
        $htmlElement = new HtmlDiv($error);
        $htmlElement->setClass('ncFormError');
        foreach ($this->getOptions()->get() as $key => $value) {
            // prevent, that already set attributes are overwritten by options
            if (!$htmlElement->hasAttribute($key)) {
                $htmlElement->setAttribute($key, $value);
            }
        }

        $htmlBuilder = new HtmlBuilder();
        return $htmlBuilder->render($htmlElement) . PHP_EOL;
    }
}