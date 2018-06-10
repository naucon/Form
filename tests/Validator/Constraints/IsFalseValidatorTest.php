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

use Naucon\Form\Validator\Constraints\IsFalse;
use Naucon\Form\Validator\Constraints\IsFalseValidator;

class IsFalseValidatorTest extends AbstractConstraintValidatorTest
{
    protected function createValidator()
    {
        return new IsFalseValidator();
    }

    public function valueProvider()
    {
        return array(
            array(true, false),
            array(false, true),
            array(0, true),
            array(1, false),
            array(2.95, false),
            array(-2.95, false),
            array('0', true),
            array('1', false),
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
        $constraint = new IsFalse(array(
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
