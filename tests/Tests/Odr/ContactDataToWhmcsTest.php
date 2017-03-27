<?php
namespace Tests\Odr;

use Tests\UnitTestCase;

class ContactDataToWhmcsTest extends UnitTestCase
{
    public function testContacts()
    {
        $testable = array(
            array(
                'source' => array(
                    'first_name'              => 'Alexander',
                    'middle_name'             => '',
                    'last_name'               => 'Kolesnikov',
                    'full_name'               => 'Alexander Kolesnikov',
                    'initials'                => 'A',
                    'gender'                  => 'NA',
                    'city'                    => 'Tomsk',
                    'language'                => 'RU',
                    'email'                   => 'hi@staticall.com',
                    'country'                 => 'RU',
                    'state'                   => 'Tomsk Region',
                    'street'                  => 'Test str.',
                    'house_number'            => '15',
                    'phone'                   => '+7.1238551441',
                    'fax'                     => null,
                    'postal_code'             => '636000',
                    'organization_legal_form' => \Odr_Whmcs::LEGAL_FORM_PERSON,
                    'company_name'            => 'Alexander Kolesnikov',
                    'company_city'            => 'Tomsk',
                    'company_email'           => 'hi@staticall.com',
                    'company_phone'           => '+7.1238551441',
                    'company_fax'             => null,
                    'company_postal_code'     => '636000',
                    'company_street'          => 'Test str.',
                    'company_house_number'    => '15',
                ),
                'expected' => array(
                    'firstname'   => 'Alexander',
                    'lastname'    => 'Kolesnikov',
                    'companyname' => '',
                    'email'       => 'hi@staticall.com',
                    'address1'    => 'Test str., 15',
                    'address2'    => '',
                    'city'        => 'Tomsk',
                    'state'       => 'Tomsk Region',
                    'postcode'    => '636000',
                    'country'     => 'RU',
                    'phonenumber' => '+7.1238551441',
                    'faxnumber'   => null,
                ),
            ),
        );

        foreach ($testable as $dataset) {
            self::assertEquals($dataset['expected'], \Odr_Whmcs::odrContactDataToWhmcs($dataset['source']));
        }
    }
}