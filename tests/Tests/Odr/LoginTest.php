<?php
namespace Tests\Odr;

use Tests\UnitTestCase;

class LoginTest extends UnitTestCase
{
    public function testLogged()
    {
        $_SESSION[\Odr_Whmcs::getSessionKey()] = array(
            'auth_name'     => 'X-Test',
            'auth_value'    => 'A',
            'expiration_at' => time() + 3600,
        );

        $module = $this->getModule();

        self::assertTrue(\Odr_Whmcs::login($module));
    }

    public function testInvalidLogin()
    {
        $module = $this->getModule();

        $module->setConfig(
            array(
                'api_key'    => 'public$failure',
                'api_secret' => 'secret$secret',
            )
        );

        self::assertEquals(\Odr_Whmcs::login($module), array('status' => \Odr_Whmcs::STATUS_ERROR, 'error' => 'Can\'t login, reason - Forced error'));
    }

    public function testException()
    {
        $module = $this->getModule();

        $module->setConfig(
            array(
                'api_key'    => 'public$success',
                'api_secret' => 'secret$throw',
            )
        );

        self::assertEquals(\Odr_Whmcs::login($module), array('status' => \Odr_Whmcs::STATUS_ERROR, 'error' => 'Can\'t login, reason - cURL error catched'));
    }

    public function testSuccess()
    {
        $module = $this->getModule();

        self::assertTrue(\Odr_Whmcs::login($module));

        self::assertEquals('token$success', $_SESSION[\Odr_Whmcs::getSessionKey()]['auth_value']);
    }
}