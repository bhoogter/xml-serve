<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

class pages_test extends TestCase
{
    private const PAGES_XML = __DIR__ . "/resources/pages.xml";
    private const SITE_XML = __DIR__ . "/resources/site.xml";

    public function testCreatePagesClass(): void
    {
        xml_serve::init(
            __DIR__ . '/resources/content',
            __DIR__,
            self::PAGES_XML,
            self::SITE_XML
        );
        $this->assertNotNull(xml_serve::$page_source);
    }

    public function testCreatePagesClassWithFile(): void
    {
        xml_serve::init(
            __DIR__ . '/resources/content',
            __DIR__,
            new xml_file(self::PAGES_XML),
            new xml_file(self::SITE_XML)
        );
        $this->assertNotNull(xml_serve::$page_source);
    }

    public function test_class_file_type(): void
    {
        $this->assertEquals("text/javascript", xml_serve::content_type("js"));
        $this->assertEquals("text/css", xml_serve::content_type("css"));
        $this->assertEquals("text/javascript", xml_serve::file_content_type("jquery-1.2.3.min.js"));
    }
}
