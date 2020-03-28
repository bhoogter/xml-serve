<?php
declare(strict_types=1);
use PHPUnit\Framework\TestCase;

class pages_test extends TestCase
{
	private const PAGES_XML = __DIR__ . "/resources/pages.xml";

	public function testCreatePagesClass(): void
	{
		$obj = new xml_pages();
		$this->assertNotNull($obj);
	}
	
	public function testCreatePagesClassWithFile(): void {
		$obj = new xml_pages(self::PAGES_XML);
		$this->assertNotNull($obj);
	}

	
}
