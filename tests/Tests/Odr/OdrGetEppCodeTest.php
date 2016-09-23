<?php
namespace Tests\Odr;

use Tests\UnitTestCase;

class OdrGetEppCodeTest extends UnitTestCase
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
            'whmcsVersion'  => '6.3.1',
            'Username'      => '',
            'ApiKey'        => 'public$live',
            'ApiSecret'     => 'secret$live',
            'TestApiKey'    => 'public$test',
            'TestApiSecret' => 'secret$test',
            'Adminuser'     => 'admin',
            'Synccontact'   => 'on',
            'Syncdomain'    => 'on',
            'Testmode'      => 'on',
            'Primarydomain' => '',
            'domainObj'     => array(),
            'domainid'      => '1',
            'domainname'    => 'test.nl',
            'sld'           => 'test',
            'tld'           => 'nl',
            'registrar'     => 'odr',
        );

        self::assertEquals(array('status' => \Odr_Whmcs::STATUS_ERROR, 'error' => 'Can\'t login, reason - Forced error'), odr_GetEPPCode($data));
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
            'whmcsVersion'  => '6.3.1',
            'Username'      => '',
            'ApiKey'        => 'public$live',
            'ApiSecret'     => 'secret$live',
            'TestApiKey'    => 'public$test',
            'TestApiSecret' => 'secret$test',
            'Adminuser'     => 'admin',
            'Synccontact'   => 'on',
            'Syncdomain'    => 'on',
            'Testmode'      => 'on',
            'Primarydomain' => '',
            'domainObj'     => array(),
            'domainid'      => '1',
            'domainname'    => 'test.nl',
            'sld'           => 'test',
            'tld'           => 'nl',
            'registrar'     => 'odr',
        );

        self::assertEquals(array('error' => 'Following error occurred: Forced error'), odr_GetEPPCode($data));
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
            'whmcsVersion'  => '6.3.1',
            'Username'      => '',
            'ApiKey'        => 'public$live',
            'ApiSecret'     => 'secret$live',
            'TestApiKey'    => 'public$test',
            'TestApiSecret' => 'secret$test',
            'Adminuser'     => 'admin',
            'Synccontact'   => 'on',
            'Syncdomain'    => 'on',
            'Testmode'      => 'on',
            'Primarydomain' => '',
            'domainObj'     => array(),
            'domainid'      => '1',
            'domainname'    => 'test.nl',
            'sld'           => 'test',
            'tld'           => 'nl',
            'registrar'     => 'odr',
        );

        self::assertEquals(array('error' => 'Following error occurred: ' . $module::MESSAGE_CURL_ERROR_FOUND), odr_GetEPPCode($data));
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
            'whmcsVersion'  => '6.3.1',
            'Username'      => '',
            'ApiKey'        => 'public$live',
            'ApiSecret'     => 'secret$live',
            'TestApiKey'    => 'public$test',
            'TestApiSecret' => 'secret$test',
            'Adminuser'     => 'admin',
            'Synccontact'   => 'on',
            'Syncdomain'    => 'on',
            'Testmode'      => 'on',
            'Primarydomain' => '',
            'domainObj'     => array(),
            'domainid'      => '1',
            'domainname'    => 'test.nl',
            'sld'           => 'test',
            'tld'           => 'nl',
            'registrar'     => 'odr',
        );

        self::assertEquals(array('eppcode' => 'e9c3de749f609f87d671111424ae2918'), odr_GetEPPCode($data));
    }

    public function testZero()
    {
        $module = $this->getModule();

        $module->setConfig(
            array(
                'api_key'    => 'public$success',
                'api_secret' => 'secret$success',
                'token'      => 'token$zero',
            )
        );

        \Odr_Whmcs::$module = $module;

        $data = array(
            'whmcsVersion'  => '6.3.1',
            'Username'      => '',
            'ApiKey'        => 'public$live',
            'ApiSecret'     => 'secret$live',
            'TestApiKey'    => 'public$test',
            'TestApiSecret' => 'secret$test',
            'Adminuser'     => 'admin',
            'Synccontact'   => 'on',
            'Syncdomain'    => 'on',
            'Testmode'      => 'on',
            'Primarydomain' => '',
            'domainObj'     => array(),
            'domainid'      => '1',
            'domainname'    => 'test.nl',
            'sld'           => 'test',
            'tld'           => 'nl',
            'registrar'     => 'odr',
        );

        self::assertEquals(array('error' => 'Either EPP code not supported or it was sent to domain owner email address'), odr_GetEPPCode($data));
    }

    public function testTrue()
    {
        $module = $this->getModule();

        $module->setConfig(
            array(
                'api_key'    => 'public$success',
                'api_secret' => 'secret$success',
                'token'      => 'token$true',
            )
        );

        \Odr_Whmcs::$module = $module;

        $data = array(
            'whmcsVersion'  => '6.3.1',
            'Username'      => '',
            'ApiKey'        => 'public$live',
            'ApiSecret'     => 'secret$live',
            'TestApiKey'    => 'public$test',
            'TestApiSecret' => 'secret$test',
            'Adminuser'     => 'admin',
            'Synccontact'   => 'on',
            'Syncdomain'    => 'on',
            'Testmode'      => 'on',
            'Primarydomain' => '',
            'domainObj'     => array(),
            'domainid'      => '1',
            'domainname'    => 'test.nl',
            'sld'           => 'test',
            'tld'           => 'nl',
            'registrar'     => 'odr',
        );

        self::assertEquals(array('eppcode' => 'EPP code was sent to domain owner email address'), odr_GetEPPCode($data));
    }
}