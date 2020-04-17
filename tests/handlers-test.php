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
    }
}
