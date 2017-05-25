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

use Naucon\Form\Mapper\Property;

/**
 * Form Helper Field Interface
 *
 * @package     Form
 * @subpackage  Helper
 * @author      Sven Sanzenbacher
 */
interface FormHelperFieldInterface extends FormHelperInterface
{
    /**
     * @return    Property
     */
    public function getProperty();

    /**
     * @param     Property      $property       from property instance
     */
    public function setProperty(Property $property);
}