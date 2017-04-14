<?php
namespace Tests\Odr;

use Tests\UnitTestCase;

class OdrSyncTest extends UnitTestCase
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

        self::assertEquals(array('status' => \Odr_Whmcs::STATUS_ERROR, 'error' => 'Can\'t login, reason - Forced error'), odr_Sync($data));
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

        self::assertEquals(array('error' => '[ERROR] Sync contact | Error retrieving domain details ODR for test.nl'), odr_Sync($data));
    }

    public function testErrorWithDomain()
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
            'domain'           => 'test.nl',
            'sld'              => 'test',
            'tld'              => 'nl',
            'registrar'        => 'odr',
            'firstname'        => 'A',
            'lastname'         => 'B',
            'companyname'      => 'C',
        );

        self::assertEquals(array('error' => '[ERROR] Sync contact | Error retrieving domain details ODR for test.nl'), odr_Sync($data));
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

        self::assertEquals(array('error' => 'Following error occurred: ' . $module::MESSAGE_CURL_ERROR_FOUND), odr_Sync($data));
    }

    public function testExceptionWithDomain()
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
            'domain'           => 'test.nl',
            'sld'              => 'test',
            'tld'              => 'nl',
            'registrar'        => 'odr',
            'firstname'        => 'A',
            'lastname'         => 'B',
            'companyname'      => 'C',
        );

        self::assertEquals(array('error' => 'Following error occurred: ' . $module::MESSAGE_CURL_ERROR_FOUND), odr_Sync($data));
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

        $result = array(
            'active'     => true,
            'expirydate' => date('Y') + 1 . '-01-01 00:00:00',
            'expired'    => false,
        );

        self::assertEquals($result, odr_Sync($data));
    }

    public function testSuccessWithDomain()
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
            'domain'           => 'test.nl',
            'sld'              => 'test',
            'tld'              => 'nl',
            'registrar'        => 'odr',
            'firstname'        => 'A',
            'lastname'         => 'B',
            'companyname'      => 'C',
        );

        $result = array(
            'active'     => true,
            'expirydate' => date('Y') + 1 . '-01-01 00:00:00',
            'expired'    => false,
        );

        self::assertEquals($result, odr_Sync($data));
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

        $result = array(
            'expired'    => true,
            'expirydate' => date('Y') + 1 . '-01-01 00:00:00',
            'active'     => false,
        );

        self::assertEquals($result, odr_Sync($data));
    }

    public function testSuccessDeletedWithDomain()
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
            'whmcsVersion'     => self::WHMCS_VERSION,
            'Username'         => '',
            'OdrApiKey'        => 'public$live',
            'OdrApiSecret'     => 'secret$live',
            'OdrTestApiKey'    => 'public$test',
            'OdrTestApiSecret' => 'secret$test',
            'OdrTestmode'      => 'on',
            'domainObj'        => array(),
            'domainid'         => '1',
            'domain'           => 'test.nl',
            'sld'              => 'test',
            'tld'              => 'nl',
            'registrar'        => 'odr',
            'firstname'        => 'A',
            'lastname'         => 'B',
            'companyname'      => 'C',
        );

        $result = array(
            'expired'    => true,
            'expirydate' => date('Y') + 1 . '-01-01 00:00:00',
            'active'     => false,
        );

        self::assertEquals($result, odr_Sync($data));
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

        $result = array(
            'expired'    => true,
            'expirydate' => date('Y') + 1 . '-01-01 00:00:00',
            'active'     => false,
        );

        self::assertEquals($result, odr_Sync($data));
    }

    public function testSuccessQuarantineWithDomain()
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
            'whmcsVersion'     => self::WHMCS_VERSION,
            'Username'         => '',
            'OdrApiKey'        => 'public$live',
            'OdrApiSecret'     => 'secret$live',
            'OdrTestApiKey'    => 'public$test',
            'OdrTestApiSecret' => 'secret$test',
            'OdrTestmode'      => 'on',
            'domainObj'        => array(),
            'domainid'         => '1',
            'domain'           => 'test.nl',
            'sld'              => 'test',
            'tld'              => 'nl',
            'registrar'        => 'odr',
            'firstname'        => 'A',
            'lastname'         => 'B',
            'companyname'      => 'C',
        );

        $result = array(
            'expired'    => true,
            'expirydate' => date('Y') + 1 . '-01-01 00:00:00',
            'active'     => false,
        );

        self::assertEquals($result, odr_Sync($data));
    }
}