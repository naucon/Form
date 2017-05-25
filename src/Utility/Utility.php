<?php
/*
 * Copyright 2008 Sven Sanzenbacher
 *
 * This file is part of the naucon package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Naucon\Form\Utility;

/**
 * Utility
 *
 * @package     Form
 * @subpackage  Utility
 * @author      Sven Sanzenbacher
 */
class Utility
{
    /**
     * normalize name eg. property name
     *
     * @param   string      $name           unnormalized name
     * @return  string      normalized name
     */
    public static function normilizeName($name)
    {
        // add underscore befor upper letters
        return strtolower(preg_replace('/(?<=[a-z])([A-Z])/', '_$1', (string)$name));
    }
}