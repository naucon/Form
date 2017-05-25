<?php
/*
 * Copyright 2008 Sven Sanzenbacher
 *
 * This file is part of the naucon package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
use Naucon\Form\FormInterface;
use Naucon\Form\FormHelper;
use Naucon\Form\Error\FormErrorsDecoratorArray;

/**
 * Smarty Form Block Plugin
 * {ncform}{/ncform}
 *
 * @package		Form
 * @subpackage	Helper
 * @author		Sven Sanzenbacher
 *
 * @param	array		$params         parameters
 * @param	string		$content        block content
 * @param	\Smarty     $smarty
 * @param	bool		$_block_repeat      block repeat
 * @return	string
 *
 * @throws \InvalidArgumentException
 */
function smarty_block_ncform($params, $content, $smarty, $_block_repeat)
{
    $form = null;
    $method = 'post';
    $action = null;
    $enctype = null;
    $options = array();

    foreach ($params as $_key => $_val) {
        switch ($_key) {
            case 'from':
                $form = $_val;
                break;
            case 'method':
                $method = $_val;
                break;
            case 'action':
                $action = $_val;
                break;
            case 'enctype':
                $enctype = $_val;
                break;
            case 'style':
            case 'class':
            case 'id':
            case 'target':
                $options[$_key] = (string)$_val;
                break;
            default:
                throw new \InvalidArgumentException("ncform: unknown attribute '$_key'");
        }
    }

    if ($form instanceof FormInterface) {
        $formHelper = new FormHelper($form);
        $smarty->assign('formHelper', $formHelper);

        $formErrors = new FormErrorsDecoratorArray($form);
        $smarty->assign('formErrors', $formErrors->getErrors());

        if (!$_block_repeat) {
            $_output = '';
            $_output.= $formHelper->formStart($method, $action, $enctype, $options);
            $_output.= $content;
            $_output.= $formHelper->formEnd();

            return $_output;
        } else {
            return null;
        }
    } else {
        throw new \InvalidArgumentException('ncform: form attribute is missing or not of type FormInterface');
    }
}