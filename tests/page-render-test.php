<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

class page_render_tests extends TestCase
{
    public static function setUpBeforeClass(): void
    {
        xml_serve::init(
            __DIR__ . "/resources/content",
            new page_source(__DIR__ . "/resources/pages.xml"),
            new xml_file(__DIR__ . "/resources/site.xml")
        );
    }

    public function candidatePage1()
    {
        $xml = "";
        $xml .= "<pagedef loc='sub1home' default='1' template='main' title='Main Page' description='a new page' keywords='a,b,c'>\n";
        $xml .= "  <content id='content' src='main-content.html' />\n";
        $xml .= "</pagedef>";

        return new xml_file($xml);
    }

    public function testDefaultLookup(): void
    {
        // php_logger::set_log_level("render_perfect", "all");
        // php_logger::set_log_level("render_linklist", "all");
        // php_logger::set_log_level("xml_serve", "all");
        php_logger::set_log_level("render_linklist", "debug");
        $candidate = $this->candidatePage1();
        $result = xml_serve::make_page($candidate);

        $xhtml = xml_file::make_tidy_string($result->saveXML(), "xml");
        print "\n---------------------------------\n{$xhtml}\n---------------------------------\n";

        $this->assertTrue(strpos($xhtml, '/content/css/global.css') !== false);
        $this->assertTrue(strpos($xhtml, '/content/templates/main/style.css') !== false);
        $this->assertTrue(strpos($xhtml, '/content/templates/main/color.css') !== false);
        $this->assertTrue(strpos($xhtml, 'a,b,c') !== false);
        $this->assertTrue(strpos($xhtml, 'Main Page - ') !== false);
        $this->assertTrue(strpos($xhtml, 'a new page') !== false);
    }
}
