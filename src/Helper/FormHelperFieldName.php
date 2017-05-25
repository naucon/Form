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

use Naucon\HtmlBuilder\HtmlFilterHtmlEntity;

/**
 * Form Helper Field Name
 *
 * @package     Form
 * @subpackage  Helper
 * @author      Sven Sanzenbacher
 */
class FormHelperFieldName extends AbstractFormHelperField
{
	/**
	 * @return    string                html output
	 */
	public function render()
	{
		$filter = new HtmlFilterHtmlEntity();
		
		return $filter->filter($this->getProperty()->getFormName());
	}
}