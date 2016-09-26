<?php
namespace Tests\Odr;

use Tests\UnitTestCase;

class OdrTransferSyncTest extends UnitTestCase
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
            'firstname'     => 'A',
            'lastname'      => 'B',
            'companyname'   => 'C',
        );

        self::assertEquals(array('status' => \Odr_Whmcs::STATUS_ERROR, 'error' => 'Can\'t login, reason - Forced error'), odr_TransferSync($data));
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
            'firstname'     => 'A',
            'lastname'      => 'B',
            'companyname'   => 'C',
        );

        self::assertEquals(array('error' => 'Following error occurred: Forced error'), odr_TransferSync($data));
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
            'firstname'     => 'A',
            'lastname'      => 'B',
            'companyname'   => 'C',
        );

        self::assertEquals(array('error' => 'Following error occurred: ' . $module::MESSAGE_CURL_ERROR_FOUND), odr_TransferSync($data));
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
            'firstname'     => 'A',
            'lastname'      => 'B',
            'companyname'   => 'C',
        );

        $result = array(
            'completed'  => true,
            'expirydate' => date('Y') + 1 . '-01-01 00:00:00',
        );

        self::assertEquals($result, odr_TransferSync($data));
    }

    public function testSuccessDeleted()
    {
        $module = $this->getModule();

        $module->setConfig(
            array(
                'api_key'    => 'public$success',
                'api_secret' => 'secret$success',
                'token'      => 'token$sudeleted',
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
            'firstname'     => 'A',
            'lastname'      => 'B',
            'companyname'   => 'C',
        );

        $result = array(
            'failed' => true,
            'reason' => 'Domain test.nl is currently in the following status: DELETED',
        );

        self::assertEquals($result, odr_TransferSync($data));
    }

    public function testSuccessQuarantine()
    {
        $module = $this->getModule();

        $module->setConfig(
            array(
                'api_key'    => 'public$success',
                'api_secret' => 'secret$success',
                'token'      => 'token$suquarantine',
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
            'firstname'     => 'A',
            'lastname'      => 'B',
            'companyname'   => 'C',
        );

        $result = array(
            'failed' => true,
            'reason' => 'Domain test.nl is currently in the following status: QUARANTINE',
        );

        self::assertEquals($result, odr_TransferSync($data));
    }

    public function testSuccessPending()
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
            'firstname'     => 'A',
            'lastname'      => 'B',
            'companyname'   => 'C',
        );

        $result = array(
            'error' => 'Domain test.nl is still in the following status: PENDING',
        );

        self::assertEquals($result, odr_TransferSync($data));
    }
}