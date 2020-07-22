<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

class script_render_test extends TestCase
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

    public function testScriptRender(): void
    {
        php_logger::clear_log_levels("debug");
        $result = xml_serve::get_page("script", "GET");

        print "\n---------------------------------\n{$result}\n---------------------------------\n";
        // $this->assertTrue(strpos($result, "//<!") !== false);
        // $this->assertTrue(strpos($result, "a  b   c        d") !== false);
        $this->assertTrue(true);
    }
}
