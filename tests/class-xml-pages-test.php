<?php
declare(strict_types=1);
use PHPUnit\Framework\TestCase;

class source_test extends TestCase
{
	public function testCreatePagesClass(): void
	{
		$obj = new xml_pages();
		$this->assertNotNull($obj);
    }
}