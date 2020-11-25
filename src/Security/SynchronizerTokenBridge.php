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

use Symfony\Component\Security\Csrf\CsrfTokenManager as BaseCsrfTokenManager;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface as BaseCsrfTokenManagerInterface;
use Symfony\Component\Security\Csrf\CsrfToken as BaseCsrfToken;

/**
 * Synchronizer Token Bridge to Symfony Security CSRF Component
 *
 * @package     Form
 * @subpackage  Security
 * @author      Sven Sanzenbacher
 */
class SynchronizerTokenBridge implements SynchronizerTokenInterface
{
    /**
     * @var     \Symfony\Component\Security\Csrf\CsrfTokenManagerInterface       synchronizer token handler
     */
    protected $handler = null;



    /**
     * Constructor
     *
     * @param       \Symfony\Component\Security\Csrf\CsrfTokenManagerInterface   $handler      optional synchronizer token handler
     */
    public function __construct(BaseCsrfTokenManagerInterface $handler=null)
    {
        if (is_null($handler)) {
            $handler = new BaseCsrfTokenManager();
        }

        $this->handler = $handler;
    }


    /**
     * @return  \Symfony\Component\Security\Csrf\CsrfTokenManagerInterface       synchronizer token handler
     */
    protected function getHandler()
    {
        return $this->handler;
    }

    /**
     * generate a synchronizer token
     *
     * @param       string      $tokenKey       token key
     * @return      string      token value
     */
    public function generateToken($tokenKey)
    {
        $csrfToken = $this->getHandler()->refreshToken($tokenKey);
        return $csrfToken->getValue();
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
        $csrfToken = new BaseCsrfToken($tokenKey, $token);
        return $this->getHandler()->isTokenValid($csrfToken);
    }

    /**
     * return token value
     *
     * @param       string      $tokenKey       token key
     * @return      string      token value
     */
    public function getToken($tokenKey)
    {
        return $this->getHandler()->getToken($tokenKey)->getValue();
    }
}
