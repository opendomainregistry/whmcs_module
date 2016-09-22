<?php
namespace Tests\Odr;

use Tests\UnitTestCase;

class OdrConfigTest extends UnitTestCase
{
    public function testModule()
    {
        $testable = array(
            array(
                'params' => array(
                    'ApiKey'        => '1',
                    'ApiSecret'     => '2',
                    'TestApiKey'    => 'a',
                    'TestApiSecret' => 'b',
                    'Testmode'      => false,
                ),
                'expected' => array(
                    'api_key'    => '1',
                    'api_secret' => '2',
                    'url'        => rtrim(\Odr_Whmcs::URL_LIVE, '/'),
                ),
            ),

            array(
                'params' => array(
                    'ApiKey'        => '1',
                    'ApiSecret'     => '2',
                    'TestApiKey'    => 'a',
                    'TestApiSecret' => 'b',
                    'Testmode'      => true,
                ),
                'expected' => array(
                    'api_key'    => 'a',
                    'api_secret' => 'b',
                    'url'        => rtrim(\Odr_Whmcs::URL_TEST, '/'),
                ),
            ),

            array(
                'params' => array(
                    'ApiKey'        => '1',
                    'ApiSecret'     => '2',
                    'TestApiKey'    => '',
                    'TestApiSecret' => '',
                    'Testmode'      => true,
                ),
                'expected' => array(
                    'api_key'    => '1',
                    'api_secret' => '2',
                    'url'        => rtrim(\Odr_Whmcs::URL_TEST, '/'),
                ),
            ),

            array(
                'params' => array(
                    'ApiKey'        => '1',
                    'ApiSecret'     => '2',
                    'TestApiKey'    => 'a',
                    'TestApiSecret' => 'b',
                    'Testmode'      => 0,
                ),
                'expected' => array(
                    'api_key'    => '1',
                    'api_secret' => '2',
                    'url'        => rtrim(\Odr_Whmcs::URL_LIVE, '/'),
                ),
            ),

            array(
                'params' => array(
                    'ApiKey'        => '1',
                    'ApiSecret'     => '2',
                    'TestApiKey'    => 'a',
                    'TestApiSecret' => 'b',
                    'Testmode'      => 1,
                ),
                'expected' => array(
                    'api_key'    => 'a',
                    'api_secret' => 'b',
                    'url'        => rtrim(\Odr_Whmcs::URL_TEST, '/'),
                ),
            ),

            array(
                'params' => array(
                    'ApiKey'        => '1',
                    'ApiSecret'     => '2',
                    'TestApiKey'    => '',
                    'TestApiSecret' => '',
                    'Testmode'      => 1,
                ),
                'expected' => array(
                    'api_key'    => '1',
                    'api_secret' => '2',
                    'url'        => rtrim(\Odr_Whmcs::URL_TEST, '/'),
                ),
            ),

            array(
                'params' => array(
                    'ApiKey'        => '1',
                    'ApiSecret'     => '2',
                    'TestApiKey'    => 'a',
                    'TestApiSecret' => 'b',
                    'Testmode'      => '0',
                ),
                'expected' => array(
                    'api_key'    => '1',
                    'api_secret' => '2',
                    'url'        => rtrim(\Odr_Whmcs::URL_LIVE, '/'),
                ),
            ),

            array(
                'params' => array(
                    'ApiKey'        => '1',
                    'ApiSecret'     => '2',
                    'TestApiKey'    => 'a',
                    'TestApiSecret' => 'b',
                    'Testmode'      => '1',
                ),
                'expected' => array(
                    'api_key'    => 'a',
                    'api_secret' => 'b',
                    'url'        => rtrim(\Odr_Whmcs::URL_TEST, '/'),
                ),
            ),

            array(
                'params' => array(
                    'ApiKey'        => '1',
                    'ApiSecret'     => '2',
                    'TestApiKey'    => '',
                    'TestApiSecret' => '',
                    'Testmode'      => '1',
                ),
                'expected' => array(
                    'api_key'    => '1',
                    'api_secret' => '2',
                    'url'        => rtrim(\Odr_Whmcs::URL_TEST, '/'),
                ),
            ),
        );

        foreach ($testable as $input) {
            $module = odr_Config($input['params']);

            self::assertEquals($input['expected'], $module->getConfig(), 'Input (' . implode(',', $input['params']) . ') not parsed correctly');
        }
    }
}