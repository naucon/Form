<?php
/*
 * Copyright 2008 Sven Sanzenbacher
 *
 * This file is part of the naucon package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Naucon\Form\Translator;

use Symfony\Contracts\Translation\TranslatorInterface as BaseTranslatorInterface;

/**
 * Translator Bridge to Symfony Translator Component
 *
 * @package     Form
 * @subpackage  Translator
 * @author      Sven Sanzenbacher
 */
class TranslatorBridge implements TranslatorInterface
{
    /**
     * @var TranslatorInterface       translator handler
     */
    protected $handler;

    /**
     * Constructor
     *
     * @param TranslatorInterface   $handler      translator handler
     */
    public function __construct(BaseTranslatorInterface $handler)
    {
        $this->setHandler($handler);
    }

    /**
     * @return TranslatorInterface       translator handler
     */
    protected function getHandler()
    {
        return $this->handler;
    }

    /**
     * @param TranslatorInterface   $handler      translator handler
     */
    protected function setHandler(BaseTranslatorInterface $handler)
    {
        $this->handler = $handler;
    }

    /**
     * {@inheritdoc}
     */
    public function trans($id, array $parameters = [], $domain = null, $locale = null): string
    {
        $tranlation = $this->getHandler()->trans($id, $parameters, $domain, $locale);

        return $tranlation;
    }

    /**
     * {@inheritdoc}
     */
    public function transChoice($id, $number, array $parameters = [], $domain = null, $locale = null)
    {
        $tranlation = $this->getHandler()->transChoice($id, $number, $parameters, $domain, $locale);

        return $tranlation;
    }

    /**
     * {@inheritdoc}
     */
    public function setLocale($locale)
    {
        $this->getHandler()->setLocale($locale);
    }

    /**
     * {@inheritdoc}
     */
    public function getLocale(): string
    {
        $locale = $this->getHandler()->getLocale();

        return $locale;
    }
}
