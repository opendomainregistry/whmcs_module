<?php
namespace Tests\Odr;

use Tests\UnitTestCase;

class OdrGetNameserversTest extends UnitTestCase
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
            'Testmode'      => 'on',
            'Primarydomain' => '',
            'domainObj'     => array(),
            'domainid'      => '1',
            'domainname'    => 'test.nl',
            'sld'           => 'test',
            'tld'           => 'nl',
            'registrar'     => 'odr',
        );

        self::assertEquals(array('status' => \Odr_Whmcs::STATUS_ERROR, 'error' => 'Can\'t login, reason - Forced error'), odr_GetNameservers($data));
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
            'Testmode'      => 'on',
            'Primarydomain' => '',
            'domainObj'     => array(),
            'domainid'      => '1',
            'domainname'    => 'test.nl',
            'sld'           => 'test',
            'tld'           => 'nl',
            'registrar'     => 'odr',
        );

        self::assertEquals(array('error' => 'Following error occurred: Forced error'), odr_GetNameservers($data));
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
            'Testmode'      => 'on',
            'Primarydomain' => '',
            'domainObj'     => array(),
            'domainid'      => '1',
            'domainname'    => 'test.nl',
            'sld'           => 'test',
            'tld'           => 'nl',
            'registrar'     => 'odr',
        );

        self::assertEquals(array('error' => 'Following error occurred: ' . $module::MESSAGE_CURL_ERROR_FOUND), odr_GetNameservers($data));
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
            'Testmode'      => 'on',
            'Primarydomain' => '',
            'domainObj'     => array(),
            'domainid'      => '1',
            'domainname'    => 'test.nl',
            'sld'           => 'test',
            'tld'           => 'nl',
            'registrar'     => 'odr',
        );

        self::assertEquals(array('ns1' => 'ns1.test.ru', 'ns2' => 'ns2.test.ru'), odr_GetNameservers($data));
    }
}