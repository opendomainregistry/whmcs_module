<?php
namespace Tests\Odr;

use Tests\UnitTestCase;

class GetModuleTest extends UnitTestCase
{
    public function testModule()
    {
        $testable = array(
            array(
                'params'   => array(
                    'OdrApiKey'        => '1',
                    'OdrApiSecret'     => '2',
                    'OdrTestApiKey'    => 'a',
                    'OdrTestApiSecret' => 'b',
                    'OdrTestmode'      => false,
                ),
                'expected' => array(
                    'api_key'    => '1',
                    'api_secret' => '2',
                    'url'        => rtrim(\Odr_Whmcs::URL_LIVE, '/'),
                ),
            ),

            array(
                'params'   => array(
                    'OdrApiKey'        => '1',
                    'OdrApiSecret'     => '2',
                    'OdrTestApiKey'    => 'a',
                    'OdrTestApiSecret' => 'b',
                    'OdrTestmode'      => true,
                ),
                'expected' => array(
                    'api_key'    => 'a',
                    'api_secret' => 'b',
                    'url'        => rtrim(\Odr_Whmcs::URL_TEST, '/'),
                ),
            ),

            array(
                'params'   => array(
                    'OdrApiKey'        => '1',
                    'OdrApiSecret'     => '2',
                    'OdrTestApiKey'    => '',
                    'OdrTestApiSecret' => '',
                    'OdrTestmode'      => true,
                ),
                'expected' => array(
                    'api_key'    => '1',
                    'api_secret' => '2',
                    'url'        => rtrim(\Odr_Whmcs::URL_TEST, '/'),
                ),
            ),

            array(
                'params'   => array(
                    'OdrApiKey'        => '1',
                    'OdrApiSecret'     => '2',
                    'OdrTestApiKey'    => 'a',
                    'OdrTestApiSecret' => 'b',
                    'OdrTestmode'      => 0,
                ),
                'expected' => array(
                    'api_key'    => '1',
                    'api_secret' => '2',
                    'url'        => rtrim(\Odr_Whmcs::URL_LIVE, '/'),
                ),
            ),

            array(
                'params'   => array(
                    'OdrApiKey'        => '1',
                    'OdrApiSecret'     => '2',
                    'OdrTestApiKey'    => 'a',
                    'OdrTestApiSecret' => 'b',
                    'OdrTestmode'      => 1,
                ),
                'expected' => array(
                    'api_key'    => 'a',
                    'api_secret' => 'b',
                    'url'        => rtrim(\Odr_Whmcs::URL_TEST, '/'),
                ),
            ),

            array(
                'params'   => array(
                    'OdrApiKey'        => '1',
                    'OdrApiSecret'     => '2',
                    'OdrTestApiKey'    => '',
                    'OdrTestApiSecret' => '',
                    'OdrTestmode'      => 1,
                ),
                'expected' => array(
                    'api_key'    => '1',
                    'api_secret' => '2',
                    'url'        => rtrim(\Odr_Whmcs::URL_TEST, '/'),
                ),
            ),

            array(
                'params'   => array(
                    'OdrApiKey'        => '1',
                    'OdrApiSecret'     => '2',
                    'OdrTestApiKey'    => 'a',
                    'OdrTestApiSecret' => 'b',
                    'OdrTestmode'      => '0',
                ),
                'expected' => array(
                    'api_key'    => '1',
                    'api_secret' => '2',
                    'url'        => rtrim(\Odr_Whmcs::URL_LIVE, '/'),
                ),
            ),

            array(
                'params'   => array(
                    'OdrApiKey'        => '1',
                    'OdrApiSecret'     => '2',
                    'OdrTestApiKey'    => 'a',
                    'OdrTestApiSecret' => 'b',
                    'OdrTestmode'      => '1',
                ),
                'expected' => array(
                    'api_key'    => 'a',
                    'api_secret' => 'b',
                    'url'        => rtrim(\Odr_Whmcs::URL_TEST, '/'),
                ),
            ),

            array(
                'params'   => array(
                    'OdrApiKey'        => '1',
                    'OdrApiSecret'     => '2',
                    'OdrTestApiKey'    => '',
                    'OdrTestApiSecret' => '',
                    'OdrTestmode'      => '1',
                ),
                'expected' => array(
                    'api_key'    => '1',
                    'api_secret' => '2',
                    'url'        => rtrim(\Odr_Whmcs::URL_TEST, '/'),
                ),
            ),
        );

        foreach ($testable as $input) {
            $module = \Odr_Whmcs::getModule($input['params']);

            $config = $module->getConfig();

            if (array_key_exists('enable_logs', $config)) {
                unset($config['enable_logs']);
            }

            if (array_key_exists('logs_path', $config)) {
                unset($config['logs_path']);
            }

            self::assertEquals($input['expected'], $config, 'Input (' . implode(',', $input['params']) . ') not parsed correctly');
        }
    }
}