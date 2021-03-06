<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

class extras {
    public function render() {
        return page_handlers::xml_content("<span>extras</span>");
    }
}



class handlers_test extends TestCase
{
	private const PAGES_XML = __DIR__ . "/resources/pages.xml";
	private const SITE_XML = __DIR__ . "/resources/pages.xml";

	public static function setUpBeforeClass(): void
	{
		xml_serve::init(
            __DIR__ . "/resources/content", 
            __DIR__, 
            self::PAGES_XML, 
            self::SITE_XML
        );
    }
    
    public function setUp(): void
    {
        xml_serve::reset_handlers();
    }

    public function testAdding(): void
    {
        $result = xml_serve::handler_list();
        $this->assertTrue(strlen($result) > 0);

        xml_serve::add_handler("one", "handler1");
        xml_serve::add_handler("one", "handler2");
        xml_serve::add_handler("one", "handler3");
        xml_serve::add_handler("one", "handler0", -1);
        $result = xml_serve::get_handlers("one");

        $this->assertEquals(4, sizeof($result));
        $this->assertEquals("handler0", $result[-1]);
        $this->assertEquals("handler1", $result[0]);
        $this->assertEquals("handler2", $result[1]);
        $this->assertEquals("handler3", $result[2]);

        xml_serve::set_handlers("two", $result);
        $this->assertEquals(count(xml_serve::get_handlers('one')), count(xml_serve::get_handlers('two')));
    }

    public function testRemoving() {
        xml_serve::add_handler("one", "handler1");
        xml_serve::add_handler("one", "handler2");
        xml_serve::add_handler("one", "handler3");
        xml_serve::add_handler("one", "handler0", -1);

        xml_serve::remove_handler("one", "handler2");
        $this->assertEquals(3, sizeof(xml_serve::get_handlers("one")));

        xml_serve::remove_handler("one", 0);
        $this->assertEquals(2, sizeof(xml_serve::get_handlers("one")));
    }

    public function testXmlContent() {
        $this->assertNotNull(page_handlers::xml_content("<sometag><other></sometag>"));
        $this->assertNotNull(page_handlers::empty_content());
    }

    public function testRenderContentDirect() {
        php_logger::clear_log_levels("all");
        xml_serve::add_handler("extra", "extras::render");
        $def = "<pagedef template='blank'><content id='content' type='element' element-name='extra' src='mode=display' /></pagedef>";
        $result = xml_serve::make_page(xml_file::toXmlFile($def));

        print "\n[[[" . xml_file::toXml($result) . "]]]";

        $this->assertTrue(!!strpos(xml_file::toXml($result), "extras"));
    }
}
