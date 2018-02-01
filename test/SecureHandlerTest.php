<?php

namespace PHPSecureSessionTest;

use PHPSecureSession\Exception\AuthenticationFailedException;
use PHPSecureSession\SecureHandler;
use PHPUnit\Framework\TestCase;
use ReflectionObject;
use ReflectionClass;
use SessionHandler;

class SecureHandlerTest extends TestCase
{
    public function setUp()
    {
        $this->secureHandler = new SecureHandler();
        session_set_save_handler($this->secureHandler, true);
        session_start();
    }

    public function tearDown()
    {
        session_destroy();
        session_write_close();
    }

    public function testConstructor()
    {
        $this->assertInstanceOf(SessionHandler::class, $this->secureHandler);
    }

    public function testOpen()
    {
        $this->assertTrue($this->secureHandler->open(sys_get_temp_dir(), ''));

        $handler = new ReflectionObject($this->secureHandler);
        $key = $handler->getProperty('key');
        $key->setAccessible(true);
        $this->assertEquals(64, mb_strlen($key->getValue($this->secureHandler), '8bit'));
    }

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

    public function testAuthenticationFailureDecrypt()
    {
        $this->assertTrue($this->secureHandler->open(sys_get_temp_dir(), ''));
        $id = session_id();
        $data = "This is a test!";
        $this->assertTrue($this->secureHandler->write($id, $data));

        // Change the session data to generate an authentication error
        $alteredData = str_replace('!', '.', $data);
        file_put_contents(sys_get_temp_dir() . "/sess_$id", $alteredData);

        $this->expectException(AuthenticationFailedException::class);
        $this->assertEquals($data, $this->secureHandler->read($id));

    }
}
