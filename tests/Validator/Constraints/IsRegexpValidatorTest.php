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

use Naucon\Form\Validator\Constraints\IsRegexp;
use Naucon\Form\Validator\Constraints\IsRegexpValidator;

class IsRegexpValidatorTest extends ConstraintValidatorTestCase
{
    protected function createValidator()
    {
        return new IsRegexpValidator();
    }

    public function valueProvider()
    {
        return array(
            array('Lorem ipsum dolor sit amet', '/(ipsum){1}/', true),
            array('Lorem ipsum', '/(ipsum){1}/', true),
            array('Lorem', '/(ipsum){1}/', false),
            array(0, '/(ipsum){1}/', false),
            array(2.95, '/(ipsum){1}/', false),
            array(-2.95, '/(ipsum){1}/', false),
            array('0', '/(ipsum){1}/', false),
            array('2.95', '/(ipsum){1}/', false),
            array('-2.95', '/(ipsum){1}/', false),
            array('abc', '/(ipsum){1}/', false),
            array('', '/(ipsum){1}/', false),
            array(null, '/(ipsum){1}/', true),
        );
    }

    /**
     * @dataProvider valueProvider
     * @param	mixed		$value				test value
     * @param   string      $regexp             regexp to validate
     * @param	bool		$expectedResult		expected result
     */
    public function testValidate($value, $regexp, $expectedResult)
    {
        $constraint = new IsRegexp(array(
            'message' => 'myMessage',
        ));

        if (!is_null($regexp)) {
            $constraint->regexp = $regexp;
        }

        $this->validator->validate($value, $constraint);

        if ($expectedResult) {
            $this->assertNoViolation();
        } else {
            $this->buildViolation('myMessage')
                ->assertRaised();
        }
    }
}
