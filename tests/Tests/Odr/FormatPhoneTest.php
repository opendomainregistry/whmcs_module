<?php
namespace Tests\Odr;

use Tests\UnitTestCase;

class FormatPhoneTest extends UnitTestCase
{
    public function testPhones()
    {
        $countries = \Odr_Whmcs::getCountries();
        $testable  = array();

        foreach ($countries as $countryCode => $country) {
            $testable[] = array(
                'country'  => $countryCode,
                'phone'    => '123123',
                'expected' => $country['phone'] . '.123123',
            );

            $testable[] = array(
                'country'  => $country['phone'],
                'phone'    => '123123',
                'expected' => $country['phone'] . '.123123',
            );
        }

        foreach ($testable as $input) {
            $result = \Odr_Whmcs::formatPhone($input['country'], $input['phone']);

            self::assertEquals($input['expected'], $result, 'Input (' . $input['country'] . ':' . $input['phone'] . ') not parsed correctly');
        }
    }
}