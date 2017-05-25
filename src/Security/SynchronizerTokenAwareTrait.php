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
 * Synchronizer Token Aware Trait
 *
 * @package     Form
 * @subpackage  Security
 * @author      Sven Sanzenbacher
 */
trait SynchronizerTokenAwareTrait
{
    /**
     * @var    SynchronizerTokenInterface
     */
    protected $synchronizerToken;

    /**
     * define synchronizer token
     *
     * @param    SynchronizerTokenInterface    $synchronizerToken      synchronizer token
     */
    public function setSynchronizerToken(SynchronizerTokenInterface $synchronizerToken)
    {
        $this->synchronizerToken = $synchronizerToken;
    }
}
