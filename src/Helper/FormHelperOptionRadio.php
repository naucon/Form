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
use Naucon\HtmlBuilder\HtmlInputRadio;

/**
 * Form Helper Option Radio
 *
 * @package     Form
 * @subpackage  Helper
 * @author      Sven Sanzenbacher
 */
class FormHelperOptionRadio extends AbstractFormHelperOption
{
    /**
     * @return    string                html output
     */
    public function render()
    {
        $htmlElement = new HtmlInputRadio($this->getForm()->getFormOptionName(), $this->getChoice());
        if ($this->getForm()->isFormOptionSelected($this->getChoice())) {
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