<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

class render_perfect_test extends TestCase
{
    const EXT_URL = "http://www.churchofthebeyond.com";
    const URL = "/about";

    public static function candidatePage()
    {
        $xml = "";
        $xml .= "<pagedef loc='sub1home' default='1' template='main' text='Main Page' description='a new page' keywords='a,b,c'>\n";
        $xml .= "  <content id='content' src='main-content.html' />\n";
        $xml .= "</pagedef>";

        return new xml_file($xml);
    }

    public static function setUpBeforeClass(): void
    {
        xml_serve::init(
            __DIR__ . "/resources/content",
            __DIR__ . "/resources",
            __DIR__ . "/resources/pages.xml",
            __DIR__ . "/resources/site.xml"
        );
        xml_serve::$pagedef = self::candidatePage();
        // php_logger::set_log_level("render_perfect", "all");
        // php_logger::set_log_level("resource_resolver", "all");
    }

    public function testPerfectUrl(): void
    {
        $this->assertEquals(self::EXT_URL, render_perfect::perfect_url(self::EXT_URL));
        $this->assertEquals("http://localhost/about", render_perfect::perfect_url("/about"));
    }

    public function testMatchUrl(): void
    {
        $this->assertEquals(self::EXT_URL, render_perfect::match_url(self::EXT_URL));
        $this->assertEquals("/about", render_perfect::match_url("/about"));

        $check = "/content/templates/main/images/logo-*.jpg";
        $result = render_perfect::match_url($check);
        $this->assertEquals(substr($check, 0, 36), substr($result, 0, 36));
        $this->assertNotEquals($check, $result);
    }
}
