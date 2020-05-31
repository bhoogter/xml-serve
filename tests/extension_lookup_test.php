<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

class extension_lookup_test extends TestCase
{

    private const SITE_XML = __DIR__ . "/resources/site.xml";
	private const PAGES_XML = __DIR__ . "/resources/pages.xml";

	public static function setUpBeforeClass(): void
	{
        require_once(__DIR__ . "/resources/content/extensions/simple-blog/functions.php");
        xml_serve::init(
            __DIR__ . '/resources/content', 
            __DIR__, 
            self::PAGES_XML, 
            self::SITE_XML
        );
        xml_serve_extensions::add_extension_handler('simple-blog', 'page', 'blog_page');
        php_logger::clear_log_levels('debug');
	}

    public function testExtensionLookup(): void 
    {
        $result = xml_serve::get_page('/sub/blog/posts/1-3-4-5', "GET");
        // print_r($result);
        $this->assertEquals(1, blog_page::$x);
        $this->assertEquals("/sub/blog", blog_page::$loc);
        $this->assertEquals("posts/1-3-4-5", blog_page::$ref);
    }

    public function testExtensionLookup2(): void 
    {
        // $this->expectException(Exception::class);
        $result = xml_serve::get_page('/blog/posts/xyz', "GET");
        // print_r($result);
        $this->assertEquals(1, blog_page::$x);
        $this->assertEquals("/blog", blog_page::$loc);
        $this->assertEquals("posts/xyz", blog_page::$ref);
    }

    public function testExtensionLookup3(): void 
    {
        // $this->expectException(Exception::class);
        $result = xml_serve::get_page('/blog2/posts/xyz123', "GET");
        // print_r($result);
        $this->assertEquals(1, blog_page::$x);
        $this->assertEquals("/blog2", blog_page::$loc);
        $this->assertEquals("posts/xyz123", blog_page::$ref);
    }
}
