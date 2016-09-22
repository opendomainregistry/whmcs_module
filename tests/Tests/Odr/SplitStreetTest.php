<?php
namespace Tests\Odr;

use Tests\UnitTestCase;

class SplitStreetTest extends UnitTestCase
{
    public function testStreets()
    {
        $testable = array(
            'Dorpstraat 2' => array(
                'street'       => 'Dorpstraat',
                'house_number' => '2',
                'additional'   => null,
            ),
            'Dorpstr. 2' => array(
                'street'       => 'Dorpstr.',
                'house_number' => '2',
                'additional'   => null,
            ),
            'Laan 1933 2' => array(
                'street'       => 'Laan 1933',
                'house_number' => '2',
                'additional'   => null,
            ),
            '18 Septemberplein 12' => array(
                'street'       => '18 Septemberplein',
                'house_number' => '12',
                'additional'   => null,
            ),
            'Kerkstraat 42-f3' => array(
                'street'       => 'Kerkstraat',
                'house_number' => '42',
                'additional'   => 'f3',
            ),
            'Kerk straat 2b' => array(
                'street'       => 'Kerk straat',
                'house_number' => '2',
                'additional'   => 'b',
            ),
            '42nd street, 1337a' => array(
                'street'       => '42nd street',
                'house_number' => '1337',
                'additional'   => 'a',
            ),
            '1e Constantijn Huigensstraat 9b' => array(
                'street'       => '1e Constantijn Huigensstraat',
                'house_number' => '9',
                'additional'   => 'b',
            ),
            'Maas-Waalweg 15' => array(
                'street'       => 'Maas-Waalweg',
                'house_number' => '15',
                'additional'   => null,
            ),
            'De Dompelaar 1 B' => array(
                'street'       => 'De Dompelaar',
                'house_number' => '1',
                'additional'   => 'B',
            ),
            'Kümmersbrucker Straße 2' => array(
                'street'       => 'Kümmersbrucker Straße',
                'house_number' => '2',
                'additional'   => null,
            ),
            'Friedrichstädter Straße 42-46' => array(
                'street'       => 'Friedrichstädter Straße',
                'house_number' => '42',
                'additional'   => '46',
            ),
            'Höhenstraße 5A' => array(
                'street'       => 'Höhenstraße',
                'house_number' => '5',
                'additional'   => 'A',
            ),
            'Saturnusstraat 60-75' => array(
                'street'       => 'Saturnusstraat',
                'house_number' => '60',
                'additional'   => '75',
            ),
            'ул. Сосновая, 14а' => array(
                'street'       => 'ул. Сосновая',
                'house_number' => '14',
                'additional'   => 'а',
            ),
            'ул. Сосновая, 14а' => array(
                'street'       => 'ул. Сосновая',
                'house_number' => '14',
                'additional'   => 'а',
            ),
            'Spuilaan 199' => array(
                'street'       => 'Spuilaan',
                'house_number' => '199',
                'additional'   => null,
            ),
            'Boffertillo 12/2' => array(
                'street'       => 'Boffertillo',
                'house_number' => '12',
                'additional'   => '2',
            ),
            'Gerronioo 52 b2' => array(
                'street'       => 'Gerronioo',
                'house_number' => '52',
                'additional'   => 'b2',
            ),
            'Suknpess 15,2' => array(
                'street'       => 'Suknpess 15',
                'house_number' => '2',
                'additional'   => null,
            ),
            'Eetapuce 255 a' => array(
                'street'       => 'Eetapuce',
                'house_number' => '255',
                'additional'   => 'a',
            ),
            '123 E Teste'   => null,
            'Test str'      => null,
            ''              => null,
            ' '             => null,
            '1'             => null,
            '@'             => null,
            '4536tggtt43tr' => null,
        );

        foreach ($testable as $input => $expected) {
            $result = \Odr_Whmcs::splitStreet($input);

            self::assertEquals($expected, $result, 'Input (' . $input . ') not parsed correctly');
        }
    }
}