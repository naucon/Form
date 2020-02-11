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

use Naucon\Form\Validator\Constraints\IsTrue;
use Naucon\Form\Validator\Constraints\IsTrueValidator;

class IsTrueValidatorTest extends ConstraintValidatorTestCase
{
    protected function createValidator()
    {
        return new IsTrueValidator();
    }

    public function valueProvider()
    {
        return [
            [true, true],
            [false, false],
            [0, false],
            [1, true],
            [2.95, false],
            [-2.95, false],
            ['0', false],
            ['1', true],
            ['2.95', false],
            ['-2.95', false],
            ['abc', false],
            ['', false],
            [null, true],
        ];
    }

    /**
     * @dataProvider valueProvider
     * @param	mixed		$value				test value
     * @param	bool		$expectedResult		expected result
     */
    public function testValidate($value, $expectedResult)
    {
        $constraint = new IsTrue(
            [
                'message' => 'myMessage',
            ]
        );

        $this->validator->validate($value, $constraint);

        if ($expectedResult) {
            $this->assertNoViolation();
        } else {
            $this->buildViolation('myMessage')
                ->assertRaised();
        }
    }
}
