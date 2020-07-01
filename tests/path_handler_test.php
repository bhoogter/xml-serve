<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

class path_handler_test extends TestCase
{
    private static $s = "/api/v1/resource/{contactId}/color";
    private static $t = "/api/v1/resource/12345/color";

    static $t1 = null;
    static $t2 = null;
    static $t3 = null;

    public static function setUpBeforeClass(): void
    {
        xml_serve::init(
            __DIR__ . "/resources/content",
            __DIR__,
            __DIR__ . "/resources/pages.xml",
            __DIR__ . "/resources/site.xml"
        );
    }

    public function testAccessor(): void
    {
        xml_path_handlers::add("/api/1", "GET", "TEST1");
        $this->assertEquals("TEST1", xml_path_handlers::get("/api/1", "GET"));
        xml_path_handlers::add("/api/2", "GET", "TEST2");
        $this->assertEquals("TEST2", xml_path_handlers::get("/api/2", "GET"));
    }

    public function testHanderPattern() {
        $a = [];
        $result = xml_path_handlers::handler_pattern(self::$s, $a);
        $this->assertEquals("\/api\/v1\/resource\/([^\/]+)\/color", $result);
        $this->assertEquals(1, count($a));
        $this->assertEquals("contactId", $a[0]);
    }

    public function testMatch(): void
    {
        xml_path_handlers::clear();
        xml_path_handlers::add(self::$s, 'GET', 'HANDLER');
        $args = [];
        $pat = '';
        $this->assertEquals('HANDLER', xml_path_handlers::match_handler(self::$t, 'GET', $args, $pat));
        $this->assertEquals(1, count($args));
        $this->assertEquals('12345', $args['contactId']);
        $this->assertEquals(self::$s, $pat);


        $this->assertNull(xml_path_handlers::match_handler("/api/v1/resource/342/colo", 'GET'));
        $this->assertNull(xml_path_handlers::match_handler("/api/v1/resource/342/", 'GET'));
        $this->assertNull(xml_path_handlers::match_handler("/api/v1/resource/342", 'GET'));
        $this->assertNull(xml_path_handlers::match_handler("/api/v1/resource/", 'GET'));
        $this->assertNull(xml_path_handlers::match_handler("/api/v1/resource", 'GET'));
        $this->assertNull(xml_path_handlers::match_handler("/api/v1/", 'GET'));
        $this->assertNull(xml_path_handlers::match_handler("/api/v1", 'GET'));
    }

    public function testMatchMulti(): void
    {
        xml_path_handlers::clear();
        xml_path_handlers::add("/api/v1/*", 'GET', 'H*');
        xml_path_handlers::add("/api/v1/*", 'POST', 'HPOST*');
        xml_path_handlers::add("/api/v1/resource/{contactId}/color", 'GET', 'Hcolor');
        xml_path_handlers::add("/api/v1/resource/{contactId}/bonder", 'GET', 'Hbonder');

        $args = [];
        $this->assertEquals('H*', xml_path_handlers::match_handler("/api/v1/jones/345", 'GET', $args, $pat));
        $this->assertEquals(1, count($args));
        $this->assertEquals('jones/345', $args['*']);

        $this->assertEquals('HPOST*', xml_path_handlers::match_handler("/api/v1/jones/345", 'POST', $args, $pat));
        $this->assertEquals(1, count($args));
        $this->assertEquals('jones/345', $args['*']);

        $this->assertEquals('Hcolor', xml_path_handlers::match_handler("/api/v1/resource/345/color", 'GET', $args, $pat));
        $this->assertEquals(1, count($args));
        $this->assertEquals('345', $args['contactId']);

        $this->assertEquals('Hbonder', xml_path_handlers::match_handler("/api/v1/resource/789/bonder", 'GET', $args, $pat));
        $this->assertEquals(1, count($args));
        $this->assertEquals('789', $args['contactId']);
    }

    public static function handler($args, $method, $pattern) {
        self::$t1 = $args;
        self::$t2 = $method;
        self::$t3 = $pattern;
        return "{ \"json\": true }";
    }

    public function testServeExtension(): void {
        // php_logger::clear_log_levels('trace');
        xml_path_handlers::clear();
        xml_path_handlers::add(self::$s, 'GET', 'path_handler_test::handler');
        xml_serve::get_page("/api/v1/resource/12345/color", "GET");

        $this->assertEquals(1, count(self::$t1));
        $this->assertEquals("12345", self::$t1['contactId']);
        $this->assertEquals("GET", self::$t2);
        $this->assertEquals(self::$s, self::$t3);
    }
}
