<?php
namespace Naucon\Form\Tests\Helper;

use Naucon\Form\Helper\AbstractFormHelperField;

class FormHelperFieldBar extends AbstractFormHelperField
{
    /**
     * @return    string
     */
    public function render()
    {
        return '<bar name="' . $this->getProperty()->getFormName() . '" />' . PHP_EOL;
    }
}