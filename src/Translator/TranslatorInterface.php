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

use Symfony\Component\Translation\TranslatorInterface as LegacyTranslatorInterface;
use Symfony\Contracts\Translation\TranslatorInterface as BaseTranslatorInterface;

/**
 * Translator Interface
 *
 * @package     Form
 * @subpackage  Translator
 * @author      Sven Sanzenbacher
 */
interface TranslatorInterface extends LegacyTranslatorInterface, BaseTranslatorInterface
{

}
