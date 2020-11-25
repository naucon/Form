<?php

namespace Naucon\Form\Tests\Security;

use Symfony\Component\Security\Csrf\Exception\TokenNotFoundException;
use Symfony\Component\Security\Csrf\TokenStorage\TokenStorageInterface;

class ArrayTokenStorage implements TokenStorageInterface
{
    /** @var string[] */
    private $tokenStore = [];

    /**
     * @param string $tokenId
     * @return string
     */
    public function getToken($tokenId)
    {
        if ($this->hasToken($tokenId) === false) {
            throw new TokenNotFoundException();
        }
        return $this->tokenStore[$tokenId];
    }

    /**
     * @param string $tokenId
     * @param string $token
     */
    public function setToken($tokenId, $token)
    {
        $this->tokenStore[$tokenId] = (string)$token;
    }

    /**
     * @param string $tokenId
     * @return string|null
     */
    public function removeToken($tokenId)
    {
        $removedToken = $this->hasToken($tokenId) ? $this->getToken($tokenId) : null;
        unset($this->tokenStore[$tokenId]);
        return $removedToken;
    }

    /**
     * @param string $tokenId
     * @return bool
     */
    public function hasToken($tokenId)
    {
        return isset($this->tokenStore[$tokenId]);
    }
}
