<?php
/*
 * Copyright 2008 Sven Sanzenbacher
 *
 * This file is part of the naucon package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Naucon\Form\Tests\Utility;

use Naucon\Form\Utility\Utility;
use PHPUnit\Framework\TestCase;

class UtilityTest extends TestCase
{
    public function nameProvider()
    {
        return array(
            array('mixedCamelCase', 'mixed_camel_case'),
            array('CamelCase', 'camel_case'),
            array('underscore_separated_case', 'underscore_separated_case'),
            array('dash-separated-case', 'dash-separated-case'),
        );
    }

    /**
     * @dataProvider nameProvider
     * @param    string         $name           name
     * @param    string         $expectedName   expected normilized name
     */
    public function testNormilizeName($name, $expectedName)
    {
        $actualName = Utility::normilizeName($name);

        $this->assertEquals($expectedName, $actualName);
    }
}
