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
 * Smarty Form Tag Function Plugin
 * {ncform_tag type='submit' value='Submit'}
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
function smarty_function_ncform_tag($params, $smarty)
{
    $formHelper = $smarty->getTemplateVars('formHelper');

    if ($formHelper instanceof FormHelper) {
        $tagType = null;
        $tagValue = null;
        $options = [];

        foreach ($params as $_key => $_val) {
            switch ($_key) {
                case 'style':
                case 'class':
                case 'id':
               	case 'name':
                    $options[$_key] = (string)$_val;
                    break;
                case 'type':
                    $tagType = $_val;
                    break;
                case 'value':
                    $tagValue = $_val;
                    break;
                default:
                    throw new \InvalidArgumentException("ncform_tag: unknown attribute '$_key'");
            }
        }

        if (!is_null($tagType)) {
            return $formHelper->formTag($tagType, $tagValue, $options);
        } else {
            return null;
        }
    } else {
        throw new \InvalidArgumentException('ncform_tag: function must be in a ncform block');
    }
}
