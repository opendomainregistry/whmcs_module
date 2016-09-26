<?php
namespace Tests\Odr;

use Tests\UnitTestCase;

class ReformatContactTest extends UnitTestCase
{
    public function testEmptyArray()
    {
        $result = \Odr_Whmcs::reformatContact(array());

        self::assertEquals(array(), $result);
    }

    public function testSingle()
    {
        $data = \Odr_Whmcs::getUselessContactData();

        $result = \Odr_Whmcs::reformatContact(array(reset($data) => 1));

        self::assertEquals(array(), $result);
    }

    public function testAll()
    {
        $data  = \Odr_Whmcs::getUselessContactData();
        $input = array();

        foreach ($data as $key) {
            $input[$key] = 'A';
        }

        $result = \Odr_Whmcs::reformatContact($input);

        self::assertEquals(array(), $result);
    }

    public function testSorted()
    {
        $data  = \Odr_Whmcs::getUselessContactData();
        $input = array(
            'ZZZ_ZZZ' => 'A',
            '___F___' => 'A',
        );

        $expected = $input;

        ksort($expected);

        foreach ($data as $key) {
            $input[$key] = 'A';
        }

        $result = \Odr_Whmcs::reformatContact($input);

        self::assertEquals($expected, $result);
    }
}