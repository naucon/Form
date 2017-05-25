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

/**
 * Translator Aware Interface
 *
 * @package     Form
 * @subpackage  Translator
 * @author      Sven Sanzenbacher
 */
interface TranslatorAwareInterface
{
    /**
     * define translator
     *
     * @param    TranslatorInterface    $translator      translator
     */
    public function setTranslator(TranslatorInterface $translator);
}
