<?php
namespace Tests\Odr;

use Tests\UnitTestCase;

class FormatContactNameTest extends UnitTestCase
{
    public function testNames()
    {
        $testable = array(
            array(
                'first'    => 'Alex',
                'last'     => '',
                'company'  => '',
                'expected' => 'Alex',
            ),
            array(
                'first'    => 'Alex',
                'last'     => 'K',
                'company'  => '',
                'expected' => 'Alex K',
            ),
            array(
                'first'    => 'Alex',
                'last'     => '',
                'company'  => 'F',
                'expected' => 'Alex (F)',
            ),
            array(
                'first'    => 'alex',
                'last'     => 'k',
                'company'  => 'f',
                'expected' => 'alex k (f)',
            ),
            array(
                'first'    => '',
                'last'     => 'k',
                'company'  => 'f',
                'expected' => 'k (f)',
            ),
            array(
                'first'    => '',
                'last'     => 'k',
                'company'  => '',
                'expected' => 'k',
            ),
            array(
                'first'    => ' ',
                'last'     => 'k',
                'company'  => 'f',
                'expected' => 'k (f)',
            ),
            array(
                'first'    => ' ',
                'last'     => ' ',
                'company'  => 'f',
                'expected' => '(f)',
            ),
            array(
                'first'    => ' ',
                'last'     => ' ',
                'company'  => ' ',
                'expected' => '',
            ),
            array(
                'first'    => ' A ',
                'last'     => ' ',
                'company'  => ' ',
                'expected' => 'A',
            ),
        );

        foreach ($testable as $input) {
            $result = \Odr_Whmcs::formatContactName($input['first'], $input['last'], $input['company']);

            self::assertEquals($input['expected'], $result, 'Input (' . $input['first'] . ':' . $input['last'] . ':' . $input['company'] . ') not parsed correctly');
        }
    }
}