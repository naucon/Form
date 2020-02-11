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

/**
 * Form Helper Interface
 *
 * @package     Form
 * @subpackage  Helper
 * @author      Sven Sanzenbacher
 */
interface FormHelperInterface
{
    /**
     * @param     array     $options
     */
    public function setOptions(array $options = []);

    /**
     * @return    string
     */
    public function render();
}
