<?php
namespace Tests\Odr;

use Tests\UnitTestCase;

class OdrRequestDeleteTest extends UnitTestCase
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
            'firstname'        => 'A',
            'lastname'         => 'B',
            'companyname'      => 'C',
        );

        self::assertEquals(array('status' => \Odr_Whmcs::STATUS_ERROR, 'error' => 'Can\'t login, reason - Forced error'), odr_RequestDelete($data));
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
            'firstname'        => 'A',
            'lastname'         => 'B',
            'companyname'      => 'C',
        );

        self::assertEquals(array('error' => 'Following error occurred: Forced error'), odr_RequestDelete($data));
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
            'firstname'        => 'A',
            'lastname'         => 'B',
            'companyname'      => 'C',
        );

        self::assertEquals(array('error' => 'Following error occurred: ' . $module::MESSAGE_CURL_ERROR_FOUND), odr_RequestDelete($data));
    }

    public function testFalse()
    {
        $module = $this->getModule();

        $module->setConfig(
            array(
                'api_key'    => 'public$success',
                'api_secret' => 'secret$success',
                'token'      => 'token$false',
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
            'firstname'        => 'A',
            'lastname'         => 'B',
            'companyname'      => 'C',
        );

        self::assertEquals(array('error' => 'Domain deletion already scheduled'), odr_RequestDelete($data));
    }

    public function testSuccess()
    {
        $module = $this->getModule();

        $module->setConfig(
            array(
                'api_key'    => 'public$success',
                'api_secret' => 'secret$success',
                'token'      => 'token$success',
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
            'firstname'        => 'A',
            'lastname'         => 'B',
            'companyname'      => 'C',
        );

        self::assertEquals(array('domainid' => '1', 'status' => 'Cancelled'), odr_RequestDelete($data));
    }
}