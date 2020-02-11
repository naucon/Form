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

use Naucon\Form\Validator\Constraints\IsIban;
use Naucon\Form\Validator\Constraints\IsIbanValidator;

class IsIbanValidatorTest extends ConstraintValidatorTestCase
{
    protected function createValidator()
    {
        return new IsIbanValidator();
    }

    public function valueProvider()
    {
        return [
            ['DE68 2105 0170 0012 3456 78', null, true],
            ['CH10 0023 00A1 0235 0260 1', null, true],
            ['CH00 0023 00A1 0235 0260 1', null, false],
            ['DE68 2105 0170 0012 3456 78', 'DE', true],
            ['CH10 0023 00A1 0235 0260 1', 'DE', false],
            ['DE68 2105 0170 0012 3456 78', 'CH', false],
            ['CH10 0023 00A1 0235 0260 1', 'CH', true],
            ['DE68 2105 0170 0012 3456 78', ['DE', 'FR'], true],
            ['CH10 0023 00A1 0235 0260 1', ['DE', 'FR'], false],
            ['DE68 2105 0170 0012 3456 78', ['CH', 'FR'], false],
            ['CH10 0023 00A1 0235 0260 1', ['CH', 'FR'], true],
        ];
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
