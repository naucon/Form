<?php
/*
 * Copyright 2008 Sven Sanzenbacher
 *
 * This file is part of the naucon package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Naucon\Form\Security;

/**
 * Null Synchronizer Token
 *
 * @package     Form
 * @subpackage  Security
 * @author      Sven Sanzenbacher
 */
class SynchronizerTokenNull implements SynchronizerTokenInterface
{
    /**
     * generate a synchronizer token
     *
     * @param       string      $tokenKey       token key
     * @return      string      token value
     */
    public function generateToken($tokenKey)
    {
        return 'TOKEN';
    }

    /**
     * validated token
     *
     * @param       string      $tokenKey       token key
     * @param       string      $token      synchronizer token from submited form data
     * @return      bool        return if a given token is valid
     */
    public function validate($tokenKey, $token)
    {
        if ($token=='TOKEN') {
            return true;
        }
        return false;
    }

    /**
     * return token value
     *
     * @param       string      $tokenKey       token key
     * @return      string      token value
     */
    public function getToken($tokenKey)
    {
        return 'TOKEN';
    }
}