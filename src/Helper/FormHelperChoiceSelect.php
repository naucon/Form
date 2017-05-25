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
use Naucon\HtmlBuilder\HtmlSelect;
use Naucon\HtmlBuilder\HtmlSelectOption;

/**
 * Form Helper Choice Select
 *
 * @package     Form
 * @subpackage  Helper
 * @author      Sven Sanzenbacher
 */
class FormHelperChoiceSelect extends AbstractFormHelperChoice
{
    /**
     * @return    string                html output
     */
    public function render()
    {
        $htmlElement = new HtmlSelect($this->getProperty()->getFormName());
        $htmlElement->setId($this->getProperty()->getFormId());

        if (count($this->getChoices()) > 0) {
            foreach ($this->getChoices() as $key => $value) {
                $htmlChildElement = new HtmlSelectOption($value, $key);

                if ($this->getProperty()->getFormValue() == $key) {
                    $htmlChildElement->setSelected(true);
                }

                $htmlElement->addChildElement($htmlChildElement);
            }
        } elseif (!is_null($this->getChoice())) {
            $htmlChildElement = new HtmlSelectOption($this->getChoice());

            if ($this->getProperty()->getFormValue() == $this->getChoice()) {
                $htmlChildElement->setSelected(true);
            }

            $htmlElement->addChildElement($htmlChildElement);
        }

        foreach ($this->getOptions()->get() as $key => $value) {
            // prevent, that already set attributes are overwritten by options
            if (!$htmlElement->hasAttribute($key)) {
                $htmlElement->setAttribute($key, $value);
            }
        }

        if ($this->getProperty()->getEntityContainer()->hasError($this->getProperty()->getName())) {
            $htmlElement->appendAttribute('class', 'nc_form_error', ' ');
        }

        $htmlBuilder = new HtmlBuilder();
        return $htmlBuilder->render($htmlElement) . PHP_EOL;
    }
}
