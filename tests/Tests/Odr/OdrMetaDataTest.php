<?php
namespace Tests\Odr;

use Tests\UnitTestCase;

class OdrMetaDataTest extends UnitTestCase
{
    public function testOptions()
    {
        $options = odr_MetaData();

        $required = array(
            'DisplayName',
            'APIVersion',
        );

        foreach ($required as $r) {
            self::assertArrayHasKey($r, $options, 'Required key "' . $r . '" is not defined');

            self::assertNotEmpty($options[$r], 'Required key "' . $r . '" is not defined');
        }
    }
}