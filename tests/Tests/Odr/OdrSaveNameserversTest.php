<?php
namespace Tests\Odr;

use Tests\UnitTestCase;

class OdrSaveNameserversTest extends UnitTestCase
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
            'whmcsVersion'     => '6.3.1',
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

        self::assertEquals(array('status' => \Odr_Whmcs::STATUS_ERROR, 'error' => 'Can\'t login, reason - Forced error'), odr_SaveNameservers($data));
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
            'whmcsVersion'     => '6.3.1',
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

        self::assertEquals(array('error' => 'Following error occurred: Forced error'), odr_SaveNameservers($data));
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
            'whmcsVersion'     => '6.3.1',
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

        self::assertEquals(array('error' => 'Following error occurred: ' . $module::MESSAGE_CURL_ERROR_FOUND), odr_SaveNameservers($data));
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
            'whmcsVersion'         => '6.3.1',
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
            'firstname'            => 'T',
            'lastname'             => 'Testov',
            'companyname'          => '',
            'regtype'              => 'Register',
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
            'adminfirstname'       => 'T',
            'adminlastname'        => 'Testov',
            'admincompanyname'     => '',
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
            'ns3'                  => '',
            'ns4'                  => '',
            'ns5'                  => '',
        );

        self::assertEquals(array(), odr_SaveNameservers($data));
    }

    public function testSuccessUpdateThrow()
    {
        $module = $this->getModule();

        $module->setConfig(
            array(
                'api_key'           => 'public$success',
                'api_secret'        => 'secret$success',
                'token'             => 'token$success',
                'tokenUpdateDomain' => 'token$thrown',
            )
        );

        \Odr_Whmcs::$module = $module;

        $data = array(
            'whmcsVersion'         => '6.3.1',
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
            'firstname'            => 'T',
            'lastname'             => 'Testov',
            'companyname'          => '',
            'regtype'              => 'Register',
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
            'adminfirstname'       => 'T',
            'adminlastname'        => 'Testov',
            'admincompanyname'     => '',
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
            'ns3'                  => '',
            'ns4'                  => '',
            'ns5'                  => '',
        );

        self::assertEquals(array('error' => 'Following error occurred: ' . $module::MESSAGE_CURL_ERROR_FOUND), odr_SaveNameservers($data));
    }

    public function testSuccessUpdateError()
    {
        $module = $this->getModule();

        $module->setConfig(
            array(
                'api_key'           => 'public$success',
                'api_secret'        => 'secret$success',
                'token'             => 'token$success',
                'tokenUpdateDomain' => 'token$error',
            )
        );

        \Odr_Whmcs::$module = $module;

        $data = array(
            'whmcsVersion'         => '6.3.1',
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
            'firstname'            => 'T',
            'lastname'             => 'Testov',
            'companyname'          => '',
            'regtype'              => 'Register',
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
            'adminfirstname'       => 'T',
            'adminlastname'        => 'Testov',
            'admincompanyname'     => '',
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
            'ns3'                  => '',
            'ns4'                  => '',
            'ns5'                  => '',
        );

        self::assertEquals(array('error' => 'Following error occurred: Forced error'), odr_SaveNameservers($data));
    }

    public function testSuccessUpdateSuccess()
    {
        $module = $this->getModule();

        $module->setConfig(
            array(
                'api_key'           => 'public$success',
                'api_secret'        => 'secret$success',
                'token'             => 'token$success',
                'tokenUpdateDomain' => 'token$success',
            )
        );

        \Odr_Whmcs::$module = $module;

        $data = array(
            'whmcsVersion'         => '6.3.1',
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
            'firstname'            => 'T',
            'lastname'             => 'Testov',
            'companyname'          => '',
            'regtype'              => 'Register',
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
            'adminfirstname'       => 'T',
            'adminlastname'        => 'Testov',
            'admincompanyname'     => '',
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
            'ns3'                  => '',
            'ns4'                  => '',
            'ns5'                  => '',
        );

        self::assertEquals(array(), odr_SaveNameservers($data));
    }
}