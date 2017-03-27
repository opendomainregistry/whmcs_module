<?php
namespace Tests\Odr;

use Tests\UnitTestCase;

class OdrSaveContactDetailsTest extends UnitTestCase
{
    public function testNotLoggedIn()
    {
        $module = $this->getModule();

        $module->setConfig(
            array(
                'api_key'    => 'public$failure',
                'api_secret' => 'secret$success',
                'token'      => 'public$success',
            )
        );

        \Odr_Whmcs::$module = $module;

        $data = $this->getDefaultData();

        self::assertEquals(array('status' => \Odr_Whmcs::STATUS_ERROR, 'error' => 'Can\'t login, reason - Forced error'), odr_SaveContactDetails($data));
    }

    public function getDefaultData()
    {
        return array(
            'whmcsVersion'     => self::WHMCS_VERSION,
            'Username'         => '',
            'OdrApiKey'        => 'public$live',
            'OdrApiSecret'     => 'secret$live',
            'OdrTestApiKey'    => 'public$test',
            'OdrTestApiSecret' => 'secret$test',
            'OdrTestmode'      => 'on',
            'domainid'         => '5',
            'domainname'       => 'fsgegrehrtjrtvwergbr.be',
            'sld'              => 'fsgegrehrtjrtvwergbr',
            'tld'              => 'be',
            'regtype'          => 'Register',
            'regperiod'        => '1',
            'registrar'        => 'odr',
            'additionalfields' => array(),
            'contactdetails'   => array(
                'Registrant' => array(
                    'firstname'   => 'TestEU',
                    'lastname'    => 'TestEU',
                    'companyname' => 'TestEU TestEU',
                    'email'       => 'staticall+whmcsprost@gmail.com',
                    'address1'    => 'Address str, 12',
                    'address2'    => '',
                    'city'        => 'Temsk',
                    'state'       => 'Noord-Brabant',
                    'postcode'    => '',
                    'country'     => 'NL',
                    'phonenumber' => '+31.9008001255',
                    'faxnumber'   => '',
                ),
                'Admin'      => array(
                    'firstname'   => 'TestEU',
                    'lastname'    => 'TestEU',
                    'companyname' => 'TestEU TestEU',
                    'email'       => 'staticall+whmcsprost@gmail.com',
                    'address1'    => 'Address str, 12',
                    'address2'    => '',
                    'city'        => 'Temsk',
                    'state'       => 'Noord-Brabant',
                    'postcode'    => '',
                    'country'     => 'NL',
                    'phonenumber' => '+31.9008001255',
                    'faxnumber'   => '',
                ),
            ),
        );
    }
}