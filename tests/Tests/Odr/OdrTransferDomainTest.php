<?php
namespace Tests\Odr;

use Tests\UnitTestCase;

class OdrTransferDomainTest extends UnitTestCase
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

        self::assertEquals(array('status' => \Odr_Whmcs::STATUS_ERROR, 'error' => 'Can\'t login, reason - Forced error'), odr_TransferDomain($data));
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

        self::assertEquals(array('error' => 'Following error occurred: Forced error'), odr_TransferDomain($data));
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

        self::assertEquals(array('error' => 'Following error occurred: ' . $module::MESSAGE_CURL_ERROR_FOUND), odr_TransferDomain($data));
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

        $data = $this->getDefaultData();

        self::assertEquals(array('success' => true), odr_TransferDomain($data));
    }

    public function testSuccessNewContact()
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

        $data = $this->getDefaultData();

        self::assertEquals(array('success' => true), odr_TransferDomain($data));
    }

    public function testSuccessNewContactFailed()
    {
        $module = $this->getModule();

        $module->setConfig(
            array(
                'api_key'            => 'public$success',
                'api_secret'         => 'secret$success',
                'token'              => 'token$success',
                'tokenCreateContact' => 'token$failure',
            )
        );

        \Odr_Whmcs::$module = $module;

        $data = $this->getDefaultData();

        self::assertEquals(array('error' => 'Following error occurred: Forced error'), odr_TransferDomain($data));
    }

    public function testSuccessNewContactThrow()
    {
        $module = $this->getModule();

        $module->setConfig(
            array(
                'api_key'            => 'public$success',
                'api_secret'         => 'secret$success',
                'token'              => 'token$success',
                'tokenCreateContact' => 'token$throw',
            )
        );

        \Odr_Whmcs::$module = $module;

        $data = $this->getDefaultData();

        self::assertEquals(array('error' => 'Following error occurred: ' . $module::MESSAGE_CURL_ERROR_FOUND), odr_TransferDomain($data));
    }

    public function testErrorInvalidAddress()
    {
        $module = $this->getModule();

        $module->setConfig(
            array(
                'api_key'            => 'public$success',
                'api_secret'         => 'secret$success',
                'token'              => 'token$success',
                'tokenCreateContact' => 'token$success',
            )
        );

        \Odr_Whmcs::$module = $module;

        $data = $this->getDefaultData();

        $data['address1'] = 'Test str.';
        $data['adminaddress1'] = 'Test str.';

        self::assertEquals(array('error' => 'Following error occurred: Invalid address'), odr_TransferDomain($data));
    }

    public function testErrorEmtpyContactId()
    {
        $module = $this->getModule();

        $module->setConfig(
            array(
                'api_key'            => 'public$success',
                'api_secret'         => 'secret$success',
                'token'              => 'token$success',
                'tokenCreateContact' => 'token$successemptyid',
            )
        );

        \Odr_Whmcs::$module = $module;

        $data = $this->getDefaultData();

        self::assertEquals(array('error' => 'Contact creation was not successful, please either try using different data or contact support'), odr_TransferDomain($data));
    }

    public function testErrorEmtpyContactIdKey()
    {
        $module = $this->getModule();

        $module->setConfig(
            array(
                'api_key'            => 'public$success',
                'api_secret'         => 'secret$success',
                'token'              => 'token$success',
                'tokenCreateContact' => 'token$successemptykey',
            )
        );

        \Odr_Whmcs::$module = $module;

        $data = $this->getDefaultData();

        self::assertEquals(array('error' => 'Contact creation was not successful, please either try using different data or contact support'), odr_TransferDomain($data));
    }

    public function testErrorTransferThrown()
    {
        $module = $this->getModule();

        $module->setConfig(
            array(
                'api_key'             => 'public$success',
                'api_secret'          => 'secret$success',
                'token'               => 'token$success',
                'tokenTransferDomain' => 'token$thrown',
            )
        );

        \Odr_Whmcs::$module = $module;

        $data = $this->getDefaultData();

        self::assertEquals(array('error' => 'Following error occurred: ' . $module::MESSAGE_CURL_ERROR_FOUND), odr_TransferDomain($data));
    }

    public function testErrorTransferError()
    {
        $module = $this->getModule();

        $module->setConfig(
            array(
                'api_key'             => 'public$success',
                'api_secret'          => 'secret$success',
                'token'               => 'token$success',
                'tokenTransferDomain' => 'token$error',
            )
        );

        \Odr_Whmcs::$module = $module;

        $data = $this->getDefaultData();

        self::assertEquals(array('error' => 'Following error occurred: Forced error'), odr_TransferDomain($data));
    }

    public function testSuccessTransferSuccess()
    {
        $module = $this->getModule();

        $module->setConfig(
            array(
                'api_key'             => 'public$success',
                'api_secret'          => 'secret$success',
                'token'               => 'token$success',
                'tokenTransferDomain' => 'token$success',
            )
        );

        \Odr_Whmcs::$module = $module;

        $data = $this->getDefaultData();

        self::assertEquals(array('success' => true), odr_TransferDomain($data));
    }

    public function getDefaultData()
    {
        return array(
            'whmcsVersion'         => self::WHMCS_VERSION,
            'Username'             => '',
            'OdrApiKey'            => 'public$live',
            'OdrApiSecret'         => 'secret$live',
            'OdrTestApiKey'        => 'public$test',
            'OdrTestApiSecret'     => 'secret$test',
            'OdrTestmode'          => 'on',
            'domainObj'            => array(),
            'domainid'             => '1',
            'domainname'           => 'test.nl',
            'sld'                  => 'test',
            'tld'                  => 'nl',
            'registrar'            => 'odr',
            'firstname'            => 'Test',
            'lastname'             => 'Testov',
            'companyname'          => 'A',
            'regtype'              => 'Transfer',
            'regperiod'            => '1',
            'additionalfields'     => [],
            'userid'               => '1',
            'id'                   => '1',
            'uuid'                 => '#UUID#',
            'fullname'             => 'T Testov',
            'email'                => 'corp@themassive.dev',
            'address1'             => 'Test str., 25',
            'address2'             => '',
            'city'                 => 'Tomsk',
            'fullstate'            => 'Tomsk Region',
            'state'                => 'Tomsk Region',
            'postcode'             => '636000',
            'countrycode'          => 'RU',
            'country'              => 'RU',
            'phonenumber'          => '1238551441',
            'password'             => '#PASSWORD#',
            'statecode'            => 'Tomsk Region',
            'countryname'          => 'Russian Federation',
            'phonecc'              => '7',
            'phonenumberformatted' => '+7.1238551441',
            'billingcid'           => '0',
            'notes'                => '',
            'twofaenabled'         => false,
            'currency'             => '1',
            'defaultgateway'       => '',
            'cctype'               => '',
            'cclastfour'           => '',
            'securityqid'          => '0',
            'securityqans'         => '',
            'groupid'              => '0',
            'status'               => 'Active',
            'credit'               => '0.00',
            'taxexempt'            => false,
            'latefeeoveride'       => false,
            'overideduenotices'    => false,
            'separateinvoices'     => false,
            'disableautocc'        => false,
            'emailoptout'          => false,
            'overrideautoclose'    => false,
            'allowSingleSignOn'    => '1',
            'language'             => '',
            'lastlogin'            => 'No Login Logged',
            'fullphonenumber'      => '+7.1238551441',
            'dnsmanagement'        => false,
            'emailforwarding'      => false,
            'idprotection'         => false,
            'adminfirstname'       => 'Test',
            'adminlastname'        => 'Testov',
            'admincompanyname'     => 'A',
            'adminemail'           => 'corp@themassive.dev',
            'adminaddress1'        => 'Test str., 25',
            'adminaddress2'        => '',
            'admincity'            => 'Tomsk',
            'adminfullstate'       => 'Tomsk Region',
            'adminstate'           => 'Tomsk Region',
            'adminpostcode'        => '636000',
            'admincountry'         => 'RU',
            'adminphonenumber'     => '1238551441',
            'adminfullphonenumber' => '+7.1238551441',
            'ns1'                  => 'ns1.yourdomain.com',
            'ns2'                  => 'ns2.yourdomain.com',
            'ns3'                  => 'ns3.yourdomain.com',
            'ns4'                  => '',
            'ns5'                  => '',
            'transfersecret'       => 'AuthCode',
        );
    }
}