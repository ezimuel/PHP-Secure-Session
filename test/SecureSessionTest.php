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
}
