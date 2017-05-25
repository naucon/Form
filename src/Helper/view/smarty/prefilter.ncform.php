<?php
/*
 * Copyright 2008 Sven Sanzenbacher
 *
 * This file is part of the naucon package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Smarty Form Prefilter Plugin
 *
 * @package		Form
 * @subpackage	Helper
 * @author		Sven Sanzenbacher
 *
 * @param	string		$tpl_source		source code
 * @param	\Smarty		$smarty
 * @return	string
 */
function smarty_prefilter_ncform($tpl_source, $smarty)
{
	$tpl = preg_replace("/\{ncform (.+)\}/", "{ncform $1}{foreach from=\$formHelper item='formEntity' name='ncform'}", $tpl_source);
	$tpl = preg_replace("/\{\/ncform\}/", '{/foreach}{/ncform}', $tpl);
	return $tpl;
}