<?php
/*
 * Copyright 2008 Sven Sanzenbacher
 *
 * This file is part of the naucon package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Naucon\Form\Validator;

use Symfony\Component\Finder\Finder;
use Symfony\Component\Validator\Validation as BaseValidation;
use Symfony\Component\Validator\ValidatorBuilderInterface as BaseValidatorBuilderInterface;
use Naucon\Form\Configuration;
use Naucon\Form\Translator\TranslatorInterface;

/**
 * Validator
 *
 * @package     Form
 * @subpackage  Translator
 * @author      Sven Sanzenbacher
 */
class Validator extends ValidatorBridge
{
    /**
     * Constructor
     *
     * @param   Configuration           $configuration      configuration
     * @param   TranslatorInterface     $translator         translator
     */
    public function __construct(Configuration $configuration, TranslatorInterface $translator=null)
    {
        // add translations from configuration
        $dirs = array();
        foreach ($configuration->get('config_paths') as $dir) {
            if (is_dir($dir)) {
                $dirs[] = $dir;
            } else {
                throw new \UnexpectedValueException(sprintf('%s defined in config_paths does not exist or is not a directory', $dir));
            }
        }

        $builder = BaseValidation::createValidatorBuilder();
        $builder->enableAnnotationMapping();
        $builder->addMethodMapping('loadValidatorMetadata');
        if (!is_null($translator)) {
            $builder->setTranslator($translator);
            $builder->setTranslationDomain('validators');
        }
        $this->addValidationMapping($builder, $dirs);
        $handler = $builder->getValidator();

        parent::__construct($handler, $translator);
    }

    /**
     * add validation mapping
     *
     * @param \Symfony\Component\Validator\ValidatorBuilderInterface  $builder
     * @param string|array  $dirs       A directory path or an array of directories
     */
    public function addValidationMapping(BaseValidatorBuilderInterface $builder, $dirs)
    {
        if (empty($dirs)) {
            return;
        }

        $finder = Finder::create()
            ->files()
            ->filter(function (\SplFileInfo $file) {
                return 1 === substr_count($file->getBasename(), '.') && preg_match('/validation\.(yml|yaml|xml)$/', $file->getBasename());
            })
            ->in($dirs)
        ;

        foreach ($finder as $file) {
            list($basename, $extension) = $parts = explode('.', $file->getBasename(), 3);

            switch ($extension) {
                case 'yml':
                case 'yaml':
                    $builder->addYamlMapping($file->getPathname());
                    break;
                case 'xml':
                    $builder->addXmlMapping($file->getPathname());
                    break;
            }
        }
    }
}