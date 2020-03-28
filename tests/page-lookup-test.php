<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

class page_lookup extends TestCase
{
	private const PAGES_XML = __DIR__ . "/resources/pages.xml";
	protected static $subject = null;

	public static function setUpBeforeClass(): void
	{
		self::$subject = new xml_pages(self::PAGES_XML, __DIR__ . "/resources");
	}

	public function testPageExactMatch(): void
	{
		$result = self::$subject->page_part("/about");
		$this->assertNotNull($result);
		$this->assertEquals("about", $result->getAttribute("loc"));
	}

	public function testDefaultLookup(): void
	{
		$result = self::$subject->page_part("/");
		$this->assertNotNull($result);
		$this->assertEquals("home", $result->getAttribute("loc"));
	}
}
