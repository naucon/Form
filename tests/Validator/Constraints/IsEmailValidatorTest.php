<?php
/*
 * Copyright 2008 Sven Sanzenbacher
 *
 * This file is part of the naucon package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Naucon\Form\Tests\Validator\Constraints;

use Naucon\Form\Validator\Constraints\IsDecimal;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Test\ConstraintValidatorTestCase;
use Naucon\Form\Validator\Constraints\IsEmail;
use Naucon\Form\Validator\Constraints\IsEmailValidator;

class IsEmailValidatorTest extends ConstraintValidatorTestCase
{
    protected function createValidator()
    {
        return new IsEmailValidator();
    }

    public function valueProvider()
    {
        return [
            [true, false],
            ['@yourdomain.com', false],
            ['yourname@yourdomain.com', true],
            ['yourname@yourdomain.co.uk', true],
            ['disposable.style.email.with+symbol@example.com', true],
            ['other.email-with-hyphen@example.com', true],
            ['user.name+tag+sorting@example.com', true],
            ['x@example.com', true],
            ['example-indeed@strange.example.com', true],
            ['example-indeed@strange-example.com', true],
            ['admin@mailserver1', false], //invalid domain name
            ['example@s.example', true],
            ['your.name@yourdomain.com', true],
            ['[your.name@yourdomain.com]', false],
            ['your_name@yourdomain.com', true],
            ["newline@yourdomain.com\n", false],
            ["newline@yourdomain.com\r", false],
            ["newline@yourdomain.com\n\r", false],
            ["[newline@yourdomain.com\n]", false],
            ['your_name@', false],
            ['this is"not\allowed@example.com', false],
            ['this\ still\"not\\allowed@example.com', false],
            ['just"not"right@example.com', false],
            ['yourname@yourdomain.com foo', false],
            ['foo yourname@yourdomain.com', false],
            ['yourname@yourdomain.com <Your Name>', false],
            ['your_name.@yourdomain.com', false],
            ['.your_name@yourdomain.com', false],
            ['-your_name@yourdomain.com', false],
            [0, false],
            [1, false],
            [2.95, false],
            [-2.95, false],
            ['0', false],
            ['2.95', false],
            ['-2.95', false],
            ['abc', false],
            ['', false],
            [null, true],
            ['olaf@-online.de', false],
            ['olaf@-online.d', false],
            ['olaf@t-onlinde', false],
            ['olaf@shw-.goldenspices.com', false],
            ['olav@gmail', false],
        ];
    }

    /**
     */
    public function testValidateWithWrongConstraint()
    {
        $this->expectException(UnexpectedTypeException::class);

        $constraint = new IsDecimal();

        $this->validator->validate('email@at.me', $constraint);
    }

    /**
     */
    public function testValidateNotMandatory()
    {
        $constraint = new IsEmail(
            [
                'message'     => 'myMessage',
                'isMandatory' => false
            ]
        );

        $this->validator->validate('', $constraint);
        $this->assertNoViolation();
    }

    /**
     * @dataProvider valueProvider
     *
     * @param mixed $value          test value
     * @param bool  $expectedResult expected result
     */
    public function testValidate($value, $expectedResult)
    {
        $constraint = new IsEmail(
            [
                'message' => 'myMessage',
            ]
        );

        $this->validator->validate($value, $constraint);

        if ($expectedResult) {
            $this->assertNoViolation();
        } else {
            $this
                ->buildViolation('myMessage')
                ->assertRaised()
            ;
        }
    }
}
