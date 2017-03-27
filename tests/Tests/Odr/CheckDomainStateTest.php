<?php
namespace Tests\Odr;

use Tests\UnitTestCase;

use WHMCS\Domains\DomainLookup\ResultsList;
use WHMCS\Domains\DomainLookup\SearchResult;

class CheckDomainsStateTest extends UnitTestCase
{
    public function testEmpty()
    {
        $module = $this->getModule();

        \Odr_Whmcs::$module = $module;

        $result = \Odr_Whmcs::checkDomainsState($module, array());

        self::assertInstanceOf(ResultsList::class, $result);

        self::assertCount(0, $result->getResults());
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

        $domains = array(
            array(
                'full' => 'test.nl',
                'sld'  => 'test',
                'tld'  => 'nl',
            ),
            array(
                'full' => 'tset.co.uk',
                'sld'  => 'tset',
                'tld'  => 'co.uk',
            ),
        );

        $result = \Odr_Whmcs::checkDomainsState($module, $domains);

        self::assertInstanceOf(ResultsList::class, $result);

        self::assertCount(count($domains), $result->getResults());

        foreach ($result->getResults() as $domain => $sr) {
            self::assertEquals($domain === 'test.nl' ? SearchResult::STATUS_NOT_REGISTERED : SearchResult::STATUS_REGISTERED, $sr);
        }
    }

    public function testError()
    {
        $module = $this->getModule();

        $module->setConfig(
            array(
                'api_key'    => 'public$success',
                'api_secret' => 'secret$success',
                'token'      => 'token$error',
            )
        );

        \Odr_Whmcs::$module = $module;

        $domains = array(
            array(
                'full' => 'test.ru',
                'sld'  => 'test',
                'tld'  => 'ru',
            ),
            array(
                'full' => 'tset.co.uk',
                'sld'  => 'tset',
                'tld'  => 'co.uk',
            ),
        );

        $result = \Odr_Whmcs::checkDomainsState($module, $domains);

        self::assertInstanceOf(ResultsList::class, $result);

        self::assertCount(count($domains), $result->getResults());

        foreach ($result->getResults() as $sr) {
            self::assertEquals(SearchResult::STATUS_REGISTERED, $sr);
        }
    }

    public function testThrown()
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

        $domains = array(
            array(
                'full' => 'test.ru',
                'sld'  => 'test',
                'tld'  => 'ru',
            ),
            array(
                'full' => 'tset.co.uk',
                'sld'  => 'tset',
                'tld'  => 'co.uk',
            ),
        );

        $result = \Odr_Whmcs::checkDomainsState($module, $domains);

        self::assertTrue(is_array($result));

        self::assertArrayHasKey('error', $result);

        self::assertEquals('Following error occurred: ' . $module::MESSAGE_CURL_ERROR_FOUND, $result['error']);
    }
}