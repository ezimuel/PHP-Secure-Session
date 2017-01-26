<?php

namespace PHPSecureSessionTest;

use PHPSecureSession\SecureHandler;
use SessionHandler;
use ReflectionObject;
use ReflectionClass;

class HashTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->secureHandler = new SecureHandler();
    }

    public function testConstructor()
    {
        $this->assertInstanceOf(SessionHandler::class, $this->secureHandler);
    }

    /**
     * @runInSeparateProcess
     */
    public function testOpen()
    {
        $this->assertTrue($this->secureHandler->open(sys_get_temp_dir(), ''));

        $handler = new ReflectionObject($this->secureHandler);
        $key = $handler->getProperty('key');
        $key->setAccessible(true);
        $this->assertEquals(64, mb_strlen($key->getValue($this->secureHandler), '8bit'));
    }

    /**
     * @runInSeparateProcess
     */
    public function testWriteRead()
    {
        $this->assertTrue($this->secureHandler->open(sys_get_temp_dir(), ''));
        $id   = session_id();
        $data = random_bytes(1024);
        $this->assertTrue($this->secureHandler->write($id, $data));
        $this->assertEquals($data, $this->secureHandler->read($id));
    }

    /**
     * @requires PHP 5.5
     */
    public function testHashEquals()
    {
        
        $class = new ReflectionClass(SecureHandler::class);
        $method = $class->getMethod('hash_equals');
        $method->setAccessible(true);

        $numBytes  = 1048576;
        $expected  = random_bytes($numBytes);
        $actual    = $expected;
        $actual[0] = chr(ord($actual[0]) + 1 % 256);


        // Compare two almost identical string (the first byte is different)
        $start = microtime(true);
        $equal = $method->invoke($this->secureHandler, $expected, $actual);
        $execTime1 = microtime(true) - $start;
        $this->assertFalse($equal);

        // Compare the same random string
        $start = microtime(true);
        $equal = $method->invoke($this->secureHandler, $expected, $expected);
        $execTime2 = microtime(true) - $start;
        $this->assertTrue($equal);

        // The difference bewteen the executtion times should be less than 30%
        $this->assertGreaterThan(0.7, $execTime1 / $execTime2);
        $this->assertLessThan(1.3, $execTime1 / $execTime2);

    }
}
