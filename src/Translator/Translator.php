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

use Symfony\Component\Translation\Formatter\MessageFormatter;
use Symfony\Component\Translation\Translator as BaseTranslator;
use Symfony\Component\Translation\Loader\XliffFileLoader;
use Symfony\Component\Translation\Loader\YamlFileLoader;
use Symfony\Component\Finder\Finder;
use Naucon\Form\Configuration;

/**
 * Translator
 *
 * @package     Form
 * @subpackage  Translator
 * @author      Sven Sanzenbacher
 */
class Translator extends TranslatorBridge
{
    /**
     * Constructor
     *
     * @param Configuration $configuration configuration
     *
     * @throws \ReflectionException
     */
    public function __construct(Configuration $configuration)
    {
        $handler = new BaseTranslator($configuration->get('locale'), new MessageFormatter());
        $handler->setFallbackLocales($configuration->get('fallback_locales'));

        $handler->addLoader('xlf', new XliffFileLoader());
        $handler->addLoader('yml', new YamlFileLoader());

        parent::__construct($handler);

        // add default translations from symfony validation component
        $dirs = array();
        if (class_exists('Symfony\Component\Validator\Validation')) {
            $r = new \ReflectionClass('Symfony\Component\Validator\Validation');
            $dirs[] = dirname($r->getFileName()) . '/Resources/translations';
        }


        // add default translations
        if (is_dir($dir = dirname(__DIR__) . '/' . '/Resources/translations')) {
            $dirs[] = $dir;
        }

        // add translations from configuration
        foreach ($configuration->get('translator_paths') as $dir) {
            if (is_dir($dir)) {
                $dirs[] = $dir;
            } else {
                throw new \UnexpectedValueException(sprintf('%s defined in translator_paths does not exist or is not a directory', $dir));
            }
        }

        $this->addResources($dirs);
    }

    /**
     * add translation resources
     *
     * @param string|array  $dirs       A directory path or an array of directories
     */
    public function addResources($dirs)
    {
        $finder = Finder::create()
            ->files()
            ->filter(function (\SplFileInfo $file) {
                return 2 === substr_count($file->getBasename(), '.') && preg_match('/\.\w+$/', $file->getBasename());
            })
            ->in($dirs)
        ;

        foreach ($finder as $file) {
            [$translationDomain, $locale, $extension] = $foo = explode('.', $file->getBasename(), 3);

            $this->handler->addResource($extension, $file->getPathname(), $locale, $translationDomain);
        }
    }
}
