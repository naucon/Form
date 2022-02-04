<?php
/*
 * Copyright 2008 Sven Sanzenbacher
 *
 * This file is part of the naucon package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Naucon\Form\Tests;

use Naucon\Form\Configuration;
use Naucon\Form\FormCollection;
use Naucon\Form\FormHelper;
use Naucon\Form\Tests\Entities\CreditCard;
use Naucon\Form\Tests\Entities\DirectDebit;
use Naucon\Form\Security\SynchronizerTokenBridge;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Csrf\CsrfTokenManager;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;
use Symfony\Component\Security\Csrf\TokenStorage\TokenStorageInterface;

class FormCollectionHelperTest extends TestCase
{
    /**
     * @var TokenGeneratorInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $generator;

    /**
     * @var CsrfTokenManager
     */
    private $manager;

    /**
     * @var TokenStorageInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $storage;

    protected function setUp()
    {
        $this->generator = $this->getMockBuilder(TokenGeneratorInterface::class)->getMock();
        $this->storage = $this->getMockBuilder(TokenStorageInterface::class)->getMock();
        $this->manager = new CsrfTokenManager($this->generator, $this->storage);
    }

    public function testFormWithCollectionTypeOne()
    {
        $synchronizerToken = new SynchronizerTokenBridge($this->manager);

        $this->storage
            ->expects($this->any())
            ->method('hasToken')
            ->with('testforms')
            ->willReturn(true);

        $this->storage
            ->expects($this->any())
            ->method('getToken')
            ->with('testforms')
            ->willReturn('TOKEN');

        $creditCardEntity = new CreditCard();
        $directDebitEntity = new DirectDebit();

        $paymentMethods = array('cc' => $creditCardEntity, 'dd' => $directDebitEntity);

        $formName = 'testforms';
        $formOptionName = $formName . '[ncform_option]';

        $configuration = new Configuration(array('collection_type' => FormCollection::COLLECTION_TYPE_ONE));
        $form = new FormCollection($paymentMethods, $formName, $configuration);
        $form->setSynchronizerToken($synchronizerToken);
        $form->bind();
        $formHelper = new FormHelper($form);

        $renderFormOptionCc = '<input type="radio" name="' . $formOptionName . '" value="cc" />' . PHP_EOL;
        $renderFormOptionDd = '<input type="radio" name="' . $formOptionName . '" value="dd" />' . PHP_EOL;
        $renderCheckedFormOptionCc = '<input type="radio" name="' . $formOptionName . '" value="cc" checked="checked" />' . PHP_EOL;
        $renderCheckedFormOptionDd = '<input type="radio" name="' . $formOptionName . '" value="dd" checked="checked" />' . PHP_EOL;

        // without default value
        $this->assertEquals($renderCheckedFormOptionCc, $formHelper->formOption('radio', 'cc'));
        $this->assertEquals($renderFormOptionDd, $formHelper->formOption('radio', 'dd'));

        // with other default value
        $form->addFormOptionDefaultValues('dd');
        $this->assertEquals($renderFormOptionCc, $formHelper->formOption('radio', 'cc'));
        $this->assertEquals($renderCheckedFormOptionDd, $formHelper->formOption('radio', 'dd'));

        // with matching default value
        $form->setFormOptionDefaultValues(array('cc'));
        $this->assertEquals($renderCheckedFormOptionCc, $formHelper->formOption('radio', 'cc'));
        $this->assertEquals($renderFormOptionDd, $formHelper->formOption('radio', 'dd'));
    }

    public function testFormWithCollectionTypeMany()
    {
        $synchronizerToken = new SynchronizerTokenBridge($this->manager);

        $this->storage
            ->expects($this->any())
            ->method('hasToken')
            ->with('testforms')
            ->willReturn(true);

        $this->storage
            ->expects($this->any())
            ->method('getToken')
            ->with('testforms')
            ->willReturn('TOKEN');

        $creditCardEntity = new CreditCard();
        $directDebitEntity = new DirectDebit();

        $paymentMethods = array('cc' => $creditCardEntity, 'dd' => $directDebitEntity);

        $formName = 'testforms';
        $method = 'post';

        $configuration = new Configuration(array('collection_type' => FormCollection::COLLECTION_TYPE_MANY));
        $form = new FormCollection($paymentMethods, $formName, $configuration);
        $form->setSynchronizerToken($synchronizerToken);
        $form->bind();
        $formHelper = new FormHelper($form);

        $renderStart = '<form method="' . $method . '" id="' . $formName . '">' . PHP_EOL;
        $renderStart .= '<input type="hidden" name="_csrf_token" value="' . $form->getSynchronizerToken() . '" />' . PHP_EOL;

        $this->assertEquals($renderStart, $formHelper->formStart());
        $this->assertEquals('</form>' . PHP_EOL, $formHelper->formEnd());
    }
}
