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
 * Synchronizer Token Interface
 *
 * @package     Form
 * @subpackage  Security
 * @author      Sven Sanzenbacher
 */
interface SynchronizerTokenInterface
{
    /**
     * generate a synchronizer token
     *
     * @param       string      $tokenKey       token key
     * @return      string      token value
     */
    public function generateToken($tokenKey);

    /**
     * validated token
     *
     * @param       string      $tokenKey       token key
     * @param       string      $token      synchronizer token from submited form data
     * @return      bool        return if a given token is valid
     */
    public function validate($tokenKey, $token);

    /**
     * return token value
     *
     * @param       string      $tokenKey       token key
     * @return      string      token value
     */
    public function getToken($tokenKey);
}