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
use Symfony\Component\Security\Csrf\TokenGenerator\UriSafeTokenGenerator as BaseUriSafeTokenGenerator;
use Symfony\Component\Security\Csrf\TokenStorage\NativeSessionTokenStorage as BaseNativeSessionTokenStorage;

/**
 * Synchronizer Token with native Session
 *
 * @package     Form
 * @subpackage  Security
 * @author      Sven Sanzenbacher
 */
class SynchronizerTokenNativeSession extends SynchronizerTokenBridge
{
    /**
     * Constructor
     */
    public function __construct()
    {
        $generator = new BaseUriSafeTokenGenerator();
        $storage = new BaseNativeSessionTokenStorage();
        $handler = new BaseCsrfTokenManager($generator, $storage);

        parent::__construct($handler);
    }
}