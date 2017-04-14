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

    public function testError()
    {
        $module = $this->getModule();

        $module->setConfig(
            array(
                'api_key'    => 'public$success',
                'api_secret' => 'secret$success',
                'token'      => 'token$failure',
            )
        );

        \Odr_Whmcs::$module = $module;

        $data = array(
            'whmcsVersion'     => self::WHMCS_VERSION,
            'Username'         => '',
            'OdrApiKey'        => 'public$live',
            'OdrApiSecret'     => 'secret$live',
            'OdrTestApiKey'    => 'public$test',
            'OdrTestApiSecret' => 'secret$test',
            'OdrTestmode'      => 'on',
            'domainObj'        => array(),
            'domainid'         => '1',
            'domainname'       => 'test.nl',
            'sld'              => 'test',
            'tld'              => 'nl',
            'registrar'        => 'odr',
        );

        self::assertEquals(array('error' => 'Following error occurred: Forced error'), odr_SaveContactDetails($data));
    }

    public function testException()
    {
        $module = $this->getModule();

        $module->setConfig(
            array(
                'api_key'    => 'public$success',
                'api_secret' => 'secret$success',
                'token'      => 'token$thrown',
            )
        );

        \Odr_Whmcs::$module = $module;

        $data = array(
            'whmcsVersion'     => self::WHMCS_VERSION,
            'Username'         => '',
            'OdrApiKey'        => 'public$live',
            'OdrApiSecret'     => 'secret$live',
            'OdrTestApiKey'    => 'public$test',
            'OdrTestApiSecret' => 'secret$test',
            'OdrTestmode'      => 'on',
            'domainObj'        => array(),
            'domainid'         => '1',
            'domainname'       => 'test.nl',
            'sld'              => 'test',
            'tld'              => 'nl',
            'registrar'        => 'odr',
        );

        self::assertEquals(array('error' => 'Following error occurred: ' . $module::MESSAGE_CURL_ERROR_FOUND), odr_SaveContactDetails($data));
    }

    public function testSuccessInfoPending()
    {
        $module = $this->getModule();

        $module->setConfig(
            array(
                'api_key'    => 'public$success',
                'api_secret' => 'secret$success',
                'token'      => 'token$supending',
            )
        );

        \Odr_Whmcs::$module = $module;

        $data = array(
            'whmcsVersion'     => self::WHMCS_VERSION,
            'Username'         => '',
            'OdrApiKey'        => 'public$live',
            'OdrApiSecret'     => 'secret$live',
            'OdrTestApiKey'    => 'public$test',
            'OdrTestApiSecret' => 'secret$test',
            'OdrTestmode'      => 'on',
            'domainObj'        => array(),
            'domainid'         => '1',
            'domainname'       => 'test.nl',
            'sld'              => 'test',
            'tld'              => 'nl',
            'registrar'        => 'odr',
        );

        self::assertEquals(array('error' => 'This domain contact details can not be changed, because only REGISTERED domains can be updated'), odr_SaveContactDetails($data));
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