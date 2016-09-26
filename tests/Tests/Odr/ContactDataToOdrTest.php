<?php
namespace Tests\Odr;

use Tests\UnitTestCase;

class ContactDataToOdrTest extends UnitTestCase
{
    public function testContacts()
    {
        $testable = array(
            array(
                'data'     => '{"whmcsVersion":"6.3.1","Username":"","ApiKey":"public$live","ApiSecret":"secret$live","TestApiKey":"public$test","TestApiSecret":"secret$test","Testmode":"on","Primarydomain":"","domainObj":{},"domainid":"1","domainname":"1.be","sld":"1","tld":"be","regtype":"Register","regperiod":"1","registrar":"odr","additionalfields":[],"userid":"1","id":"1","uuid":"#UUID#","firstname":"Alexander","lastname":"Kolesnikov","fullname":"Alexander Kolesnikov","companyname":"","email":"staticall@themassive.dev","address1":"Test str., 15","address2":"","city":"Tomsk","fullstate":"Tomsk Region","state":"Tomsk Region","postcode":"636000","countrycode":"RU","country":"RU","phonenumber":"1238551441","password":"#PASSWORD#","statecode":"Tomsk Region","countryname":"Russian Federation","phonecc":"7","phonenumberformatted":"+7.1238551441","billingcid":"0","notes":"","twofaenabled":false,"currency":"1","defaultgateway":"","cctype":"","cclastfour":"","securityqid":"0","securityqans":"","groupid":"0","status":"Active","credit":"0.00","taxexempt":false,"latefeeoveride":false,"overideduenotices":false,"separateinvoices":false,"disableautocc":false,"emailoptout":false,"overrideautoclose":false,"allowSingleSignOn":"1","language":"","lastlogin":"No Login Logged","fullphonenumber":"+7.1238551441","dnsmanagement":false,"emailforwarding":false,"idprotection":false,"adminfirstname":"Alexander","adminlastname":"Kolesnikov","admincompanyname":"","adminemail":"staticall@themassive.dev","adminaddress1":"Test str., 15","adminaddress2":"","admincity":"Tomsk","adminfullstate":"Tomsk Region","adminstate":"Tomsk Region","adminpostcode":"636000","admincountry":"RU","adminphonenumber":"1238551441","adminfullphonenumber":"+7.1238551441","ns1":"ns1.yourdomain.com","ns2":"ns2.yourdomain.com","ns3":"","ns4":"","ns5":""}',
                'expected' => array(
                    'first_name'              => 'Alexander',
                    'middle_name'             => '',
                    'last_name'               => 'Kolesnikov',
                    'full_name'               => 'Alexander Kolesnikov',
                    'initials'                => 'A',
                    'gender'                  => 'NA',
                    'city'                    => 'Tomsk',
                    'language'                => 'RU',
                    'email'                   => 'staticall@themassive.dev',
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
                    'company_email'           => 'staticall@themassive.dev',
                    'company_phone'           => '+7.1238551441',
                    'company_fax'             => null,
                    'company_postal_code'     => '636000',
                    'company_street'          => 'Test str.',
                    'company_house_number'    => '15',
                ),
            ),

            array(
                'data'     => '{"whmcsVersion":"6.3.1","Username":"","ApiKey":"public$live","ApiSecret":"secret$live","TestApiKey":"public$test","TestApiSecret":"secret$test","Testmode":"on","Primarydomain":"","domainObj":{},"domainid":"1","domainname":"1.be","sld":"1","tld":"be","regtype":"Register","regperiod":"1","registrar":"odr","additionalfields":[],"userid":"1","id":"1","uuid":"#UUID#","firstname":"Test","lastname":"Testov","fullname":"Test Testov","companyname":"Testing inc.","email":"corp@themassive.dev","address1":"Test str., 25","address2":"","city":"Tomsk","fullstate":"Tomsk Region","state":"Tomsk Region","postcode":"636000","countrycode":"RU","country":"RU","phonenumber":"1238551441","password":"#PASSWORD#","statecode":"Tomsk Region","countryname":"Russian Federation","phonecc":"7","phonenumberformatted":"+7.1238551441","billingcid":"0","notes":"","twofaenabled":false,"currency":"1","defaultgateway":"","cctype":"","cclastfour":"","securityqid":"0","securityqans":"","groupid":"0","status":"Active","credit":"0.00","taxexempt":false,"latefeeoveride":false,"overideduenotices":false,"separateinvoices":false,"disableautocc":false,"emailoptout":false,"overrideautoclose":false,"allowSingleSignOn":"1","language":"","lastlogin":"No Login Logged","fullphonenumber":"+7.1238551441","dnsmanagement":false,"emailforwarding":false,"idprotection":false,"adminfirstname":"Test","adminlastname":"Testov","admincompanyname":"Testing inc.","adminemail":"corp@themassive.dev","adminaddress1":"Test str., 25","adminaddress2":"","admincity":"Tomsk","adminfullstate":"Tomsk Region","adminstate":"Tomsk Region","adminpostcode":"636000","admincountry":"RU","adminphonenumber":"1238551441","adminfullphonenumber":"+7.1238551441","ns1":"ns1.yourdomain.com","ns2":"ns2.yourdomain.com","ns3":"","ns4":"","ns5":""}',
                'expected' => array(
                    'first_name'              => 'Test',
                    'middle_name'             => '',
                    'last_name'               => 'Testov',
                    'full_name'               => 'Test Testov (Testing inc.)',
                    'initials'                => 'T',
                    'gender'                  => 'NA',
                    'city'                    => 'Tomsk',
                    'language'                => 'RU',
                    'email'                   => 'corp@themassive.dev',
                    'country'                 => 'RU',
                    'state'                   => 'Tomsk Region',
                    'street'                  => 'Test str.',
                    'house_number'            => '25',
                    'phone'                   => '+7.1238551441',
                    'fax'                     => null,
                    'postal_code'             => '636000',
                    'organization_legal_form' => \Odr_Whmcs::LEGAL_FORM_COMPANY,
                    'company_name'            => 'Testing inc.',
                    'company_city'            => 'Tomsk',
                    'company_email'           => 'corp@themassive.dev',
                    'company_phone'           => '+7.1238551441',
                    'company_fax'             => null,
                    'company_postal_code'     => '636000',
                    'company_street'          => 'Test str.',
                    'company_house_number'    => '25',
                ),
            ),

            array(
                'data'     => '{"whmcsVersion":"6.3.1","Username":"","ApiKey":"public$live","ApiSecret":"secret$live","TestApiKey":"public$test","TestApiSecret":"secret$test","Testmode":"on","Primarydomain":"","domainObj":{},"domainid":"1","domainname":"1.be","sld":"1","tld":"be","regtype":"Register","regperiod":"1","registrar":"odr","additionalfields":[],"userid":"1","id":"1","uuid":"#UUID#","firstname":"Test","lastname":"Testov","fullname":"Test Testov","companyname":"Testing inc.","email":"corp@themassive.dev","address1":"Test str.","address2":"","city":"Tomsk","fullstate":"Tomsk Region","state":"Tomsk Region","postcode":"636000","countrycode":"RU","country":"RU","phonenumber":"1238551441","password":"#PASSWORD#","statecode":"Tomsk Region","countryname":"Russian Federation","phonecc":"7","phonenumberformatted":"+7.1238551441","billingcid":"0","notes":"","twofaenabled":false,"currency":"1","defaultgateway":"","cctype":"","cclastfour":"","securityqid":"0","securityqans":"","groupid":"0","status":"Active","credit":"0.00","taxexempt":false,"latefeeoveride":false,"overideduenotices":false,"separateinvoices":false,"disableautocc":false,"emailoptout":false,"overrideautoclose":false,"allowSingleSignOn":"1","language":"","lastlogin":"No Login Logged","fullphonenumber":"+7.1238551441","dnsmanagement":false,"emailforwarding":false,"idprotection":false,"adminfirstname":"Test","adminlastname":"Testov","admincompanyname":"Testing inc.","adminemail":"corp@themassive.dev","adminaddress1":"Test str.","adminaddress2":"","admincity":"Tomsk","adminfullstate":"Tomsk Region","adminstate":"Tomsk Region","adminpostcode":"636000","admincountry":"RU","adminphonenumber":"1238551441","adminfullphonenumber":"+7.1238551441","ns1":"ns1.yourdomain.com","ns2":"ns2.yourdomain.com","ns3":"","ns4":"","ns5":""}',
                'expected' => 'Invalid address',
            ),
        );

        foreach ($testable as $k => $input) {
            if (is_string($input['data'])) {
                $input['data'] = json_decode($input['data'], true);
            }

            $result = \Odr_Whmcs::contactDataToOdr($input['data']);

            self::assertEquals($input['expected'], $result, 'Input #' . ($k + 1) . ' not parsed correctly');
        }
    }
}