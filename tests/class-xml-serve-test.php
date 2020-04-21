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
            new page_source(self::PAGES_XML), 
            new site_settings(self::SITE_XML)
        );
        $this->assertNotNull(xml_serve::$page_source);
    }
}
