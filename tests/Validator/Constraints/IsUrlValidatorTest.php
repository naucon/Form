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

use Naucon\Form\Validator\Constraints\IsUrl;
use Naucon\Form\Validator\Constraints\IsUrlValidator;

class IsUrlValidatorTest extends AbstractConstraintValidatorTest
{
    protected function createValidator()
    {
        return new IsUrlValidator();
    }

    public function valueProvider()
    {
        return array(
            array(true, false),
            array('www.yourdomain.de', false),
            array('yourdomain.de', false),
            array('http://www.yourdomain.de', true),
            array('http://www.yourdomain.de/foo', true),
            array('http://www.yourdomain.de/foo/', true),
            array('http://www.yourdomain.de/foo/bar.html', true),
            array('http://www.yourdomain.de/foo/?arg1=1&arg2=2', true),
            array('http://www.yourdomain.de/foo/bar.html?arg1=1&arg2=2', true),
            array('http://127.0.0.1/foo/bar.html?arg1=1&arg2=2', true),
            array('http://127.0.0.1/foo/bar.html?arg[]=1&arg[]=2', true),
            array(0, false),
            array(1, false),
            array(2.95, false),
            array(-2.95, false),
            array('0', false),
            array('2.95', false),
            array('-2.95', false),
            //array('abc', false), // TODO regexp should not match
            //array('', false), // TODO regexp should not match
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
        $constraint = new IsUrl(array(
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
