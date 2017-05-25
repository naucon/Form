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
use Naucon\HtmlBuilder\HtmlInputCheckbox;

/**
 * Form Helper Choice Checkbox
 *
 * @package     Form
 * @subpackage  Helper
 * @author      Sven Sanzenbacher
 */
class FormHelperChoiceCheckbox extends AbstractFormHelperChoice
{
    /**
     * @return    string            html output
     */
    public function render()
    {
        $htmlElement = new HtmlInputCheckbox($this->getProperty()->getFormName(), $this->getChoice());
        $htmlElement->setId($this->getProperty()->getFormId());

        if ($this->getProperty()->getFormValue() == $this->getChoice()) {
            $htmlElement->setChecked(true);
        }

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