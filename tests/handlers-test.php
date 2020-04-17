<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

class handlers_test extends TestCase
{
	private const PAGES_XML = __DIR__ . "/resources/pages.xml";
	private const SITE_XML = __DIR__ . "/resources/pages.xml";
	protected static $subject = null;

	public static function setUpBeforeClass(): void
	{
		self::$subject = new xml_serve(__DIR__ . "/resources", self::PAGES_XML, self::SITE_XML);
	}

    public function testAdding(): void
    {
        $result = self::$subject::handler_list();
        $this->assertTrue(strlen($result) > 0);

        self::$subject::add_handler("one", "handler1");
        self::$subject::add_handler("one", "handler2");
        self::$subject::add_handler("one", "handler3");
        self::$subject::add_handler("one", "handler0", -1);
        $result = self::$subject::get_handlers("one");

        $this->assertEquals(4, sizeof($result));
        $this->assertEquals("handler0", $result[-1]);
        $this->assertEquals("handler1", $result[0]);
        $this->assertEquals("handler2", $result[1]);
        $this->assertEquals("handler3", $result[2]);
    }
}
