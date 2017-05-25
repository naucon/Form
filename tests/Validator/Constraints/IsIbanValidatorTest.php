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

use Naucon\Form\Validator\Constraints\IsIban;
use Naucon\Form\Validator\Constraints\IsIbanValidator;

class IsIbanValidatorTest extends AbstractConstraintValidatorTest
{
    protected function createValidator()
    {
        return new IsIbanValidator();
    }

    public function valueProvider()
    {
        return array(
            array('DE68 2105 0170 0012 3456 78', null, true),
            array('CH10 0023 00A1 0235 0260 1', null, true),
            array('CH00 0023 00A1 0235 0260 1', null, false),
            array('DE68 2105 0170 0012 3456 78', 'DE', true),
            array('CH10 0023 00A1 0235 0260 1', 'DE', false),
            array('DE68 2105 0170 0012 3456 78', 'CH', false),
            array('CH10 0023 00A1 0235 0260 1', 'CH', true),
            array('DE68 2105 0170 0012 3456 78', array('DE', 'FR'), true),
            array('CH10 0023 00A1 0235 0260 1', array('DE', 'FR'), false),
            array('DE68 2105 0170 0012 3456 78', array('CH', 'FR'), false),
            array('CH10 0023 00A1 0235 0260 1', array('CH', 'FR'), true),
        );
    }

    /**
     * @dataProvider valueProvider
     * @param	string		$value				test iban
     * @param   string      $countryCodes       null, one or a array of ISO2 country codes
     * @param	bool		$expectedResult		expected result
     */
    public function testValidate($value, $countryCodes, $expectedResult)
    {
        $constraint = new IsIban(array(
            'message' => 'myMessage',
        ));

        if (!is_null($countryCodes)) {
            $constraint->countryCodes = $countryCodes;
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
