<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

class pages_test extends TestCase
{
    private const PAGES_XML = __DIR__ . "/resources/pages.xml";
    private const SITE_XML = __DIR__ . "/resources/site.xml";

    public function testCreatePagesClass(): void
    {
        $obj = new xml_serve(__DIR__ . '/resources', self::PAGES_XML, self::SITE_XML);
        $this->assertNotNull($obj);
    }

    public function testCreatePagesClassWithFile(): void
    {
        $obj = new xml_serve(__DIR__ . '/resources', self::PAGES_XML, self::SITE_XML);
        $this->assertNotNull($obj);
    }

    public function testCreatePagesClassWithXmlFileObject(): void
    {
        $xmlobj = new xml_serve(__DIR__ . '/resources', self::PAGES_XML, self::SITE_XML);
        $this->assertNotNull($xmlobj);
    }
}
