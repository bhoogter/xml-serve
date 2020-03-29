<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

class resource_resolver_tests extends TestCase
{
	public static function setUpBeforeClass(): void
	{
		resource_resolver::init(__DIR__ . "/resources/content");
	}

	public function fileFound($spec, $types = [], $mappings = []) {
		$res = resource_resolver::resolve_file($spec, $types, $mappings);
		if ($res == null) return false;
		return substr($res, -strlen($spec)) == $spec;
	}

	public function testResolveHtmlFile(): void
	{
		$this->assertTrue($this->fileFound("main-content.html"));
		$this->assertTrue($this->fileFound("contact-content.html"));
		$this->assertTrue($this->fileFound("about-content.html"));
	}
}
