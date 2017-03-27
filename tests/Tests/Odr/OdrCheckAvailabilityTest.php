<?php
namespace Tests\Odr;

use Tests\UnitTestCase;

use WHMCS\Domains\DomainLookup\ResultsList;
use WHMCS\Domains\DomainLookup\SearchResult;

class OdrCheckAvailabilityTest extends UnitTestCase
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
            'sld'              => 'test',
            'tld'              => 'nl',
            'searchTerm'       => null,
            'tldsToInclude'    => array(),
        );

        self::assertEquals(array('status' => \Odr_Whmcs::STATUS_ERROR, 'error' => 'Can\'t login, reason - Forced error'), odr_CheckAvailability($data));
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

        $data = $this->getDefaultData();

        $result = odr_CheckAvailability($data);

        self::assertInstanceOf(ResultsList::class, $result);

        $res = $result->getResults();

        self::assertArrayHasKey('test.nl', $res);

        self::assertEquals(SearchResult::STATUS_REGISTERED, $res['test.nl']);
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
            'sld'              => 'test',
            'tld'              => 'nl',
            'searchTerm'       => null,
            'tldsToInclude'    => array(),
        );

        self::assertEquals(array('error' => 'Following error occurred: ' . $module::MESSAGE_CURL_ERROR_FOUND), odr_CheckAvailability($data));
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

        $result = odr_CheckAvailability($data);

        self::assertInstanceOf(ResultsList::class, $result);

        $res = $result->getResults();

        self::assertArrayHasKey('test.nl', $res);

        self::assertEquals(SearchResult::STATUS_NOT_REGISTERED, $res['test.nl']);
    }

    public function testSuccessTldInclude()
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

        $data['sld'] = null;
        $data['tld'] = null;

        $data['searchTerm'] = 'test';

        $data['tldsToInclude'] = array(
            '.nl',
            '.eu',
            '.ru',
            '.be',
            '.com',
        );

        $result = odr_CheckAvailability($data);

        self::assertInstanceOf(ResultsList::class, $result);

        self::assertCount(count($data['tldsToInclude']), $result->getResults());
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
            'sld'              => 'test',
            'tld'              => 'nl',
            'searchTerm'       => null,
            'tldsToInclude'    => array(),
        );
    }
}