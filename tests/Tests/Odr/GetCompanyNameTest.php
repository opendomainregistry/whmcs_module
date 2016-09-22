<?php
namespace Tests\Odr;

use Tests\UnitTestCase;

class GetCompanyNameTest extends UnitTestCase
{
    public function testCompanies()
    {
        $testable = array(
            array(
                'company'  => 'Company',
                'name'     => 'Name',
                'expected' => 'Company',
            ),
            array(
                'company'  => '',
                'name'     => 'Name',
                'expected' => 'Name',
            ),
            array(
                'company'  => 'Company ',
                'name'     => 'Name',
                'expected' => 'Company ',
            ),
            array(
                'company'  => '',
                'name'     => ' Name ',
                'expected' => ' Name ',
            ),
            array(
                'company'  => false,
                'name'     => true,
                'expected' => true,
            ),
            array(
                'company'  => '0',
                'name'     => ' ',
                'expected' => ' ',
            ),
        );

        foreach ($testable as $input) {
            $result = \Odr_Whmcs::getCompanyName($input['company'], $input['name']);

            self::assertEquals($input['expected'], $result, 'Input (' . $input['company'] . ':' . $input['name'] . ') not parsed correctly');
        }
    }
}