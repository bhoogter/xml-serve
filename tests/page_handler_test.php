<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

use function PHPSTORM_META\elementType;

class page_handler_test extends TestCase
{
    static $one = false;
    static $two = false;

    public static function reset() {
        self::$one = false;
        self::$two = false;
    }

    public static function setUpBeforeClass(): void
    {
        page_handlers::add_handler("one", get_class() . "::handleOne");
        page_handlers::add_handler("two", get_class() . "::handleTwo");
    }

    public static function initialElement($type) {
        $x = "";
        $x .= "<div>";
        $x .= "  <$type />";
        $x .= "</div>";
        return $x;
    }

    public static function newElement() {
        $x = "";
        $x .= "<div>";
        $x .= "---";
        $x .= "</div>";
        return $x;
    }

    public function handleOne($el, $params = [], $vArgs = "") {
        self::$one = true;
        return xml_file::toDocEl(self::newElement());
    }

    public function handleTwo($el, $params = [], $vArgs = "") {
        self::$two = true;
        return self::newElement();        
    }

    public function testHandleElement(): void
    {
        self::reset();
        page_handlers::handle_element("one", xml_file::toDocEl(self::initialElement('one')));
        $this->assertTrue(self::$one);
    }

    public function testHandleString(): void
    {
        self::reset();
        $result = page_handlers::handle_element("two", xml_file::toDocEl(self::initialElement('two')));
        $this->assertTrue(self::$two);
        print(xml_file::toXml($result));
    }
}
