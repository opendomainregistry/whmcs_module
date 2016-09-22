<?php
namespace Tests\Odr;

use Tests\UnitTestCase;

class GetCountryTest extends UnitTestCase
{
    public function testPhones()
    {
        $countries = \Odr_Whmcs::getCountries();
        $testable  = array(
            array(
                'input'    => 'aaa',
                'expected' => $countries['nl'],
            ),
            array(
                'input'    => 'b',
                'expected' => $countries['nl'],
            ),
            array(
                'input'    => '1',
                'expected' => $countries['nl'],
            ),
            array(
                'input'    => '0',
                'expected' => $countries['nl'],
            ),
            array(
                'input'    => '',
                'expected' => $countries['nl'],
            ),
        );

        foreach ($countries as $countryCode => $country) {
            $testable[] = array(
                'input'    => $countryCode,
                'expected' => $country,
            );

            $testable[] = array(
                'input'    => strtoupper($countryCode),
                'expected' => $country,
            );
        }

        foreach ($testable as $input) {
            $result = \Odr_Whmcs::getCountry($input['input']);

            self::assertEquals($input['expected'], $result, 'Input (' . $input['input'] . ') not parsed correctly');
        }
    }
}