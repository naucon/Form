<?php
namespace Naucon\Form\Tests\Helper;

use Naucon\Form\Helper\AbstractFormHelperField;

class FormHelperFieldFoo extends AbstractFormHelperField
{
    /**
     * @return    string
     */
    public function render()
    {
        return '<foo name="' . $this->getProperty()->getFormName() . '" />' . PHP_EOL;
    }
}