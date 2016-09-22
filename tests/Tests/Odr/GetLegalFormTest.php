<?php
namespace Tests\Odr;

use Tests\UnitTestCase;

class GetLegalFormTest extends UnitTestCase
{
    public function testForms()
    {
        $testable = array(
            array(
                'value'    => '',
                'expected' => \Odr_Whmcs::LEGAL_FORM_PERSON,
            ),
            array(
                'value'    => '1',
                'expected' => \Odr_Whmcs::LEGAL_FORM_COMPANY,
            ),
            array(
                'value'    => '0',
                'expected' => \Odr_Whmcs::LEGAL_FORM_PERSON,
            ),
            array(
                'value'    => false,
                'expected' => \Odr_Whmcs::LEGAL_FORM_PERSON,
            ),
            array(
                'value'    => null,
                'expected' => \Odr_Whmcs::LEGAL_FORM_PERSON,
            ),
            array(
                'value'    => true,
                'expected' => \Odr_Whmcs::LEGAL_FORM_COMPANY,
            ),
            array(
                'value'    => ' ',
                'expected' => \Odr_Whmcs::LEGAL_FORM_COMPANY,
            ),
            array(
                'value'    => 'a',
                'expected' => \Odr_Whmcs::LEGAL_FORM_COMPANY,
            ),
        );

        foreach ($testable as $input) {
            $result = \Odr_Whmcs::getLegalForm($input['value']);

            self::assertEquals($input['expected'], $result, 'Input (' . $input['value'] . ') not parsed correctly');
        }
    }
}