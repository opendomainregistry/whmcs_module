<?php
namespace Tests\Odr;

use Tests\UnitTestCase;

class MapContactTypeToOdrTest extends UnitTestCase
{
    public function testEmptyValue()
    {
        $result = \Odr_Whmcs::mapContactTypeToOdr('');

        self::assertNull($result);
    }

    public function testUnknownValue()
    {
        $result = \Odr_Whmcs::mapContactTypeToOdr('_');

        self::assertNull($result);
    }

    public function testCorrectValues()
    {
        $map = \Odr_Whmcs::getContactTypesMap();

        foreach ($map as $from => $to) {
            self::assertEquals($to, \Odr_Whmcs::mapContactTypeToOdr($from));
        }
    }
}