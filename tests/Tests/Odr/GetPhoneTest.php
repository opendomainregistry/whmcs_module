<?php
namespace Tests\Odr;

use Tests\UnitTestCase;

class GetPhoneTest extends UnitTestCase
{
    public function testPhones()
    {
        $testable = array(
            '+1.123456'      => '123456',
            '+7.123.456'     => '123456',
            '1.123 456'      => '123456',
            '.1.123456'      => '123456',
            '+1.123456+'     => '123456',
            '+1.123)456'     => '123456',
            '+1.123)456.'    => '123456',
            '+1.(123) 456'   => '123456',
            '+1.(123) 14-42' => '1231442',
            '+1123)456.'     => '1123456',
            '+1123)45.6'     => '6',
        );

        foreach ($testable as $input => $expected) {
            $result = \Odr_Whmcs::getPhone($input);

            self::assertEquals($expected, $result, 'Input (' . $input . ') not parsed correctly');
        }
    }
}