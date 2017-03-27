<?php
namespace Tests\Odr;

use Tests\UnitTestCase;

class MapContactTypeToWhmcsTest extends UnitTestCase
{
    public function testEmptyValue()
    {
        $result = \Odr_Whmcs::mapContactTypeToWhmcs('');

        self::assertNull($result);
    }

    public function testUnknownValue()
    {
        $result = \Odr_Whmcs::mapContactTypeToWhmcs('_');

        self::assertNull($result);
    }

    public function testCorrectValues()
    {
        $map = array_flip(\Odr_Whmcs::getContactTypesMap());

        foreach ($map as $from => $to) {
            self::assertEquals($to, \Odr_Whmcs::mapContactTypeToWhmcs($from));
        }
    }
}