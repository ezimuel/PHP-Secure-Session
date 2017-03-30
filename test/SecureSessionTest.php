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
     * Test for issue #27
     * @see https://github.com/ezimuel/PHP-Secure-Session/issues/27
     *
     * @runInSeparateProcess
     */
    public function testDoubleOpen()
    {
        $this->assertTrue($this->secureHandler->open(sys_get_temp_dir(), ''));
        $id1 = session_id();

        $handler = new ReflectionObject($this->secureHandler);
        $key = $handler->getProperty('key');
        $key->setAccessible(true);
        $key1 = $key->getValue($this->secureHandler);

        $this->assertTrue($this->secureHandler->open(sys_get_temp_dir(), ''));
        $id2 = session_id();
        $key2 = $key->getValue($this->secureHandler);

        $this->assertEquals($id1, $id2);
        $this->assertEquals($key1, $key2);
    }
}
