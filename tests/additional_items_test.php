<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

class additional_items_test extends TestCase
{
    public static function setUpBeforeClass(): void
    {
        xml_serve::init(
            __DIR__ . "/resources/content",
            __DIR__ . "/resources",
            __DIR__ . "/resources/pages.xml",
            __DIR__ . "/resources/site.xml"
        );
    }

    public function testAdditionalItems(): void
    {
        // php_logger::set_log_level('xml_serve', 'all');
        xml_serve::$additional_css[] = "/content/modules/test1/test1.css";
        xml_serve::add_additional_css("/content/modules/test2/test2.css");

        xml_serve::add_additional_rss("/content/modules/rss/feed.rss");

        xml_serve::add_additional_header("<junk />");
        xml_serve::add_additional_meta("meta1", "value1");

        $result = xml_serve::get_page("/", "GET");
        print substr($result, 0, strpos($result, '<body>'));
        // print_r(xml_serve::$additional_css);
        // print_r(xml_serve::$additional_headers);

        $this->assertTrue(false != strpos($result, '<link rel="stylesheet" type="text/css" href="/content/css/global.css" />'));
        $this->assertTrue(false != strpos($result, '<link rel="stylesheet" type="text/css" href="/content/templates/main/style.css" />'));
        $this->assertTrue(false != strpos($result, '<global-junk />'));

        $this->assertTrue(false != strpos($result, '<link rel="stylesheet" type="text/css" href="/content/modules/test1/test1.css" />'));
        $this->assertTrue(false != strpos($result, '<link rel="stylesheet" type="text/css" href="/content/modules/test2/test2.css" />'));

        $this->assertTrue(false != strpos($result, '<link rel="alternate" type="application/rss+xml" href="/content/modules/rss/feed.rss" />'));
        
        $this->assertTrue(false != strpos($result, '<junk />'));
        $this->assertTrue(false != strpos($result, '<meta name="meta1" content="value1" />'));
    }
}
