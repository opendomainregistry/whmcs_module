<?php
namespace Tests\Odr;

use Tests\UnitTestCase;

class OdrGetConfigArrayTest extends UnitTestCase
{
    public function testOptions()
    {
        $options = odr_getConfigArray();

        $required = array(
            'FriendlyName',
            'Description',
            'ApiKey',
            'ApiSecret',
            'TestApiKey',
            'TestApiSecret',
            'Testmode',
        );

        foreach ($required as $r) {
            self::assertArrayHasKey($r, $options, 'Required key "' . $r . '" is not defined');

            self::assertNotEmpty($options[$r], 'Required key "' . $r . '" is not defined');
        }
    }
}