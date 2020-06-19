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
        xml_serve::$additional_css[] = "/content/modules/test2/test2.css";

        $result = xml_serve::get_page("/", "GET");


        $t1 = '<link rel="stylesheet" type="text/css" href="/content/modules/test1/test1.css" />';
        $t2 = '<link rel="stylesheet" type="text/css" href="/content/modules/test2/test2.css" />';

        $this->assertTrue(false != strpos($result, $t1));
        $this->assertTrue(false != strpos($result, $t2));
    }
}
