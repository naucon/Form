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

use Symfony\Component\Translation\IdentityTranslator as BaseIdentityTranslator;

/**
 * Null Translator
 * translate nothing
 *
 * @package     Form
 * @subpackage  Translator
 * @author      Sven Sanzenbacher
 */
class TranslatorNull extends BaseIdentityTranslator implements TranslatorInterface
{
}