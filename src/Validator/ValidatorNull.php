<?php
/*
 * Copyright 2008 Sven Sanzenbacher
 *
 * This file is part of the naucon package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Naucon\Form\Validator;

use Naucon\Form\Exception\InvalidArgumentException;

/**
 * Null Validator
 *
 * @package     Form
 * @subpackage  Validator
 * @author      Sven Sanzenbacher
 */
class ValidatorNull implements ValidatorInterface
{
    /**
     * validate data given entity
     *
     * @param   object      $entity         entity
     * @return  array
     */
    public function validate($entity)
    {
        if (!is_object($entity)) {
            throw new InvalidArgumentException('given entity is no a object');
        }

        return [];
    }

}
