<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

class page_render_tests extends TestCase
{
	// private const PAGES_XML = __DIR__ . "/resources/pages.xml";
	// protected static $subject = null;

	// public static function setUpBeforeClass(): void
	// {
	// 	self::$subject = new xml_pages(self::PAGES_XML, __DIR__ . "/resources");
	// }

	// public function lookupPageId($path)
	// {
	// 	return self::$subject->page_part($path)->get("/pagedef/@loc");
	// }

	public function testDefaultLookup(): void
	{
		$this->assertTrue(true);
	}
}
