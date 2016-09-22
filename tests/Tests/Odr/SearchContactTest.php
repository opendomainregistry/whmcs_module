<?php
namespace Tests\Odr;

use Tests\UnitTestCase;

class SearchContactTest extends UnitTestCase
{
    public function testPhones()
    {
        $testable = array(
            array(
                'name'     => 'Test Testov',
                'contacts' => array(
                    array(
                        'id'   => 1,
                        'name' => 'Test Testov ',
                    ),
                    array(
                        'id'   => 2,
                        'name' => 'Test Testov',
                    ),
                ),
                'expected' => 2,
            ),
            array(
                'name'     => 'Test Testov',
                'contacts' => array(
                    array(
                        'id'   => 1,
                        'name' => 'Test Testov ',
                    ),
                    array(
                        'id'   => 2,
                        'name' => ' Test Testov',
                    ),
                ),
                'expected' => null,
            ),
            array(
                'name'     => 'Test Testov',
                'contacts' => array(
                    array(
                        'id'   => 1,
                        'name' => 'Test Testov',
                    ),
                    array(
                        'id'   => 2,
                        'name' => 'Test Testov',
                    ),
                ),
                'expected' => 1,
            ),
            array(
                'name'     => 'Test Testov (F)',
                'contacts' => array(
                    array(
                        'id'   => 1,
                        'name' => 'Test Testov (F) [Test]',
                    ),
                    array(
                        'id'   => 2,
                        'name' => 'Test Testov (F) [Test2]',
                    ),
                ),
                'expected' => 1,
            ),
            array(
                'name'     => 'Test Testov (Fr)',
                'contacts' => array(
                    array(
                        'id'   => 1,
                        'name' => 'Test Testov (F) [Test]',
                    ),
                    array(
                        'id'   => 2,
                        'name' => 'Test Testov (F) [Test2]',
                    ),
                ),
                'expected' => null,
            ),
            array(
                'name'     => 'Test Testov (F)',
                'contacts' => array(
                ),
                'expected' => null,
            ),
            array(
                'name'     => 'Test Testov',
                'contacts' => array(
                    array(
                        'id'   => 2,
                        'name' => 'Test Testov  [Test2]',
                    ),
                ),
                'expected' => 2,
            ),
            array(
                'name'     => 'Test Testov',
                'contacts' => array(
                    array(
                        'id'   => 2,
                        'name' => ' Test Testov [Test2]',
                    ),
                ),
                'expected' => 2,
            ),
            array(
                'name'     => 'Test Testov',
                'contacts' => array(
                    array(
                        'id'   => 2,
                        'name' => ' Test Testov  [Test2]',
                    ),
                ),
                'expected' => 2,
            ),
        );

        foreach ($testable as $input) {
            $result = \Odr_Whmcs::searchContact($input['name'], $input['contacts']);

            self::assertEquals($input['expected'], $result, 'Input (' . $input['name'] . ') not parsed correctly');
        }
    }
}