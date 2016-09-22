<?php
namespace Tests\Odr;

use Tests\UnitTestCase;

class GetPostalCodeTest extends UnitTestCase
{
    public function testCodes()
    {
        $testable = array(
            array(
                'value'    => '1234 AB',
                'expected' => '1234AB',
            ),
            array(
                'value'    => ' ',
                'expected' => '',
            ),
            array(
                'value'    => '0',
                'expected' => '0',
            ),
            array(
                'value'    => false,
                'expected' => '',
            ),
            array(
                'value'    => null,
                'expected' => '',
            ),
            array(
                'value'    => true,
                'expected' => '1',
            ),
            array(
                'value'    => '1234AB',
                'expected' => '1234AB',
            ),
            array(
                'value'    => '634000',
                'expected' => '634000',
            ),
            array(
                'value'    => 'ABCD  432',
                'expected' => 'ABCD432',
            ),
            array(
                'value'    => ' a B c D ',
                'expected' => 'ABCD',
            ),
            array(
                'value'    => 'a',
                'expected' => 'A',
            ),
            array(
                'value'    => '1234ab',
                'expected' => '1234AB',
            ),
        );

        foreach ($testable as $input) {
            $result = \Odr_Whmcs::getPostalCode($input['value']);

            self::assertEquals($input['expected'], $result, 'Input (' . $input['value'] . ') not parsed correctly');
        }
    }
}