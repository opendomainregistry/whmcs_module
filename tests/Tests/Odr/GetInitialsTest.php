<?php
namespace Tests\Odr;

use Tests\UnitTestCase;

class GetInitialsTest extends UnitTestCase
{
    public function testInitialsFull()
    {
        $testable = array(
            'Pavel Petrov'            => 'PP',
            'Павел Петров'            => 'ПП',
            'Anton Olegovich Pushkin' => 'AOP',
            'anton pavlov'            => 'AP',
        );

        foreach ($testable as $input => $expected) {
            $result = \Odr_Whmcs::getInitialsFull($input);

            self::assertEquals($expected, $result, 'Input (' . $input . ') not parsed correctly');
        }
    }

    public function testInitials()
    {
        $testable = array(
            'Jarr van derr Post' => 'J',
            'alan malan'         => 'A',
            'alex flade'         => 'A',
            'Hard van der toast' => 'H',
        );

        foreach ($testable as $input => $expected) {
            $result = \Odr_Whmcs::getInitials($input);

            self::assertEquals($expected, $result, 'Input (' . $input . ') not parsed correctly');
        }
    }
}