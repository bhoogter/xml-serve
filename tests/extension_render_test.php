<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

class extension_render_test extends TestCase
{
    public static function setUpBeforeClass(): void
    {
        xml_serve::init(
            __DIR__ . "/resources/content",
            __DIR__,
            __DIR__ . "/resources/pages.xml",
            __DIR__ . "/resources/site.xml"
        );

    }

    public function candidatePage1()
    {
        $xml = "";
        $xml .= "<pagedef loc='blog' extension='simple-blog' default='1' template='main' text='Main Page' description='a new page' keywords='a,b,c'>\n";
        $xml .= "  <content id='content' src='simple-blog-content.xml' />\n";
        $xml .= "</pagedef>";

        return new xml_file($xml);
    }

    public function testDefaultLookup(): void
    {
        // php_logger::set_log_level("xml_serve", "all");
        // php_logger::set_log_level("page_source", "all");
        // php_logger::set_log_level("render_perfect", "all");
        // php_logger::set_log_level("render_linklist", "all");
        // php_logger::set_log_level("xml_serve", "all");
        // php_logger::set_log_level("page_handlers", "all");
        // php_logger::set_log_level("render_linklist", "debug");
        php_logger::set_log_level("render_content", "debug");
        php_logger::set_log_level("resource_resolver", "all");
        $candidate = $this->candidatePage1();
        xml_serve::$extension = "simple-blog";
        $result = xml_serve::make_page($candidate);

        $xhtml = xml_file::make_tidy_string($result->saveXML(), "xml");
        print "\n---------------------------------\n{$xhtml}\n---------------------------------\n";

        php_logger::set_log_level('resource_resolver', 'trace');

        $this->assertTrue(strpos($xhtml, '/content/css/global.css') !== false);
        $this->assertTrue(strpos($xhtml, '/content/templates/main/style.css') !== false);
        $this->assertTrue(strpos($xhtml, '/content/templates/main/color.css') !== false);
        $this->assertTrue(strpos($xhtml, 'a,b,c') !== false);
        $this->assertTrue(strpos($xhtml, 'Main Page') !== false);
        $this->assertTrue(strpos($xhtml, 'a new page') !== false);
    }
}
