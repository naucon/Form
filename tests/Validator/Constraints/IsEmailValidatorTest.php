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

use Symfony\Component\Validator\Tests\Constraints\AbstractConstraintValidatorTest;

use Naucon\Form\Validator\Constraints\IsEmail;
use Naucon\Form\Validator\Constraints\IsEmailValidator;

class IsEmailValidatorTest extends AbstractConstraintValidatorTest
{
    protected function createValidator()
    {
        return new IsEmailValidator();
    }

    public function valueProvider()
    {
        return array(
            array(true, false),
            array('yourname@yourdomain', false),
            array('@yourdomain.com', false),
            array('yourname@yourdomain.com', true),
            array('yourname@yourdomain.co.uk', true),
            array('your.name@yourdomain.com', true),
            array('your_name@yourdomain.com', true),
            array('your_name@', false),
            array('yourname@yourdomain.com foo', false),
            array('foo yourname@yourdomain.com', false),
            array('yourname@yourdomain.com <Your Name>', false),
            array(0, false),
            array(1, false),
            array(2.95, false),
            array(-2.95, false),
            array('0', false),
            array('2.95', false),
            array('-2.95', false),
            array('abc', false),
            array('', false),
            array(null, true),
        );
    }

    /**
     * @dataProvider valueProvider
     * @param	mixed		$value				test value
     * @param	bool		$expectedResult		expected result
     */
    public function testValidate($value, $expectedResult)
    {
        $constraint = new IsEmail(array(
            'message' => 'myMessage',
        ));

        $this->validator->validate($value, $constraint);

        if ($expectedResult) {
            $this->assertNoViolation();
        } else {
            $this->buildViolation('myMessage')
                ->assertRaised();
        }
    }
}
