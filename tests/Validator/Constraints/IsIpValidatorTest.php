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

use Symfony\Component\Validator\Test\ConstraintValidatorTestCase;

use Naucon\Form\Validator\Constraints\IsIp;
use Naucon\Form\Validator\Constraints\IsIpValidator;

class IsIpValidatorTest extends ConstraintValidatorTestCase
{
    protected function createValidator()
    {
        return new IsIpValidator();
    }

    public function valueProvider()
    {
        return array(
            array(true, false),
            array('www.yourdomain.de', false),
            array('yourdomain.de', false),
            array('192.168.0.100', true),
            array('127.0.0.1', true),
            array('127/0/0/1', false),
            array('127 0 0 1', false),
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
        $constraint = new IsIp(array(
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
