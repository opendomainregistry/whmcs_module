<?php
namespace Tests;

use Mocks;

abstract class UnitTestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * @var bool
     *
     * @private
     */
    private $_isLoaded = false;

    /**
     * @var array
     *
     * @protected
     *
     * @static
     */
    static protected $_isAvailable = array();

    /**
     * Setup the test
     *
     * @return null
     *
     * @protected
     */
    protected function setUp()
    {
        parent::setUp();

        $_REQUEST = array();
        $_SESSION = array();
        $_COOKIE  = array();

        $this->_isLoaded = true;
    }

    /**
     * Tear the test down
     *
     * @return null
     *
     * @protected
     */
    protected function tearDown()
    {
        parent::tearDown();
    }

    /**
     * Check if the test case is setup properly
     *
     * @return null
     *
     * @throws \PHPUnit_Framework_IncompleteTestError
     */
    public function __destruct()
    {
        if (!$this->_isLoaded) {
            throw new \PHPUnit_Framework_IncompleteTestError('Please run parent::setUp()');
        }
    }

    /**
     * Checks if a particular extension is loaded and if not, marks the test as skipped
     *
     * @param string $extension Extension to check
     *
     * @return bool
     *
     * @static
     */
    static public function checkExtension($extension)
    {
        if (!extension_loaded($extension)) {
            self::markTestSkipped("Warning: Extension '{$extension}' is not loaded");

            return false;
        }

        return true;
    }

    /**
     * Returns accessible protected or private method for testing
     *
     * @param string $className  Target class name
     * @param string $methodName Target method name
     *
     * @return \ReflectionMethod
     *
     * @throws \ReflectionException If method not exists
     */
    public function getSecureMethod($className, $methodName)
    {
        $reflection = new \ReflectionClass($className);

        $method = $reflection->getMethod($methodName);

        $method->setAccessible(true);

        return $method;
    }

    /**
     * Returns accessible protected or private property for testing
     *
     * @param string $className    Target class name
     * @param string $propertyName Target property name
     *
     * @return \ReflectionProperty
     *
     * @throws \ReflectionException If method not exists
     */
    public function getSecureProperty($className, $propertyName)
    {
        $reflection = new \ReflectionClass($className);

        $property = $reflection->getProperty($propertyName);

        $property->setAccessible(true);

        return $property;
    }

    public function getModule()
    {
        $module = new Mocks\Odr(
            array(
                'api_key'    => 'public$success',
                'api_secret' => 'secret$success',
                'url'        => \Odr_Whmcs::URL_TEST,
            )
        );

        return $module;
    }
}