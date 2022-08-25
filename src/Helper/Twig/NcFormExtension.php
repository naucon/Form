<?php

namespace Naucon\Form\Helper\Twig;

use InvalidArgumentException;
use Naucon\Form\FormHelper;
use Naucon\Form\FormInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class NcFormExtension extends AbstractExtension
{
    public function getFunctions()
    {
        return [
            new TwigFunction('ncform_start', [$this, 'formStart'], ['is_safe' => ['html']]),
            new TwigFunction('ncform_end', [$this, 'formEnd'], ['is_safe' => ['html']]),
            new TwigFunction('ncform_field', [$this, 'field'], ['is_safe' => ['html']])
        ];
    }

    public function formStart(
        FormInterface $form,
        string        $method = 'post',
        ?string       $action = null,
        ?string       $enctype = null,
        array         $options = []
    ): string
    {
        $formHelper = new FormHelper($form);

        return $formHelper->formStart($method, $action, $enctype, $options);
    }

    public function formEnd(FormInterface $form): string
    {
        $formHelper = new FormHelper($form);

        return $formHelper->formEnd();
    }

    public function field(FormInterface $form, string $type, string $field, ?array $options = []): string
    {
        $formHelper = new FormHelper($form);
        $validOptions = ['style', 'class', 'id', 'value', 'maxlength', 'required', 'data-'];

        foreach (array_keys($options) as $option) {
            $isInvalid = true;
            foreach ($validOptions as $validOption) {
                if (strpos($option, $validOption) !== false) {
                    $isInvalid = false;
                }
            }
            if ($isInvalid) {
                throw new InvalidArgumentException(
                    'Only following options are allowed: ' . implode(', ', $validOptions)
                );
            }
        }

        return $formHelper->formField($type, $field, $options);
    }
}
