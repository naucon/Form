<?php
/*
 * Copyright 2008 Sven Sanzenbacher
 *
 * This file is part of the naucon package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
use Naucon\Form\FormHelper;

/**
 * Smarty Form Field Function Plugin
 * {ncform_field type='text' field='username'}
 *
 * @package		Form
 * @subpackage	Helper
 * @author		Sven Sanzenbacher
 *
 * @param	array		$params     parameters
 * @param	\Smarty     $smarty
 * @return	string
 *
 * @throws \InvalidArgumentException
 */
function smarty_function_ncform_field($params, $smarty)
{
    $formHelper = $smarty->getTemplateVars('formHelper');

    if ($formHelper instanceof FormHelper) {
        $fieldName = null;
        $fieldType = null;
        $options = array();

        foreach ($params as $_key => $_val) {
            switch ($_key) {
                case 'style':
                case 'class':
                case 'id':
                case 'value':
                case 'maxlength':
                case 'required':
                    $options[$_key] = (string)$_val;
                    break;
                case 'type':
                    $fieldType = $_val;
                    break;
                case 'field':
                    $fieldName = $_val;
                    break;
                default:
                    if (strrpos($_key, 'data-') == 0) {
                        $options[$_key] = (string)$_val;
                        break;
                    }
                    throw new \InvalidArgumentException("ncform_field: unknown attribute '$_key'");
            }
        }

        if (!is_null($fieldName) && !is_null($fieldType)) {
            return $formHelper->formField($fieldType, $fieldName, $options);
        } else {
            return null;
        }
    } else {
        throw new \InvalidArgumentException('ncform_field: function must be in a ncform block');
    }
}
