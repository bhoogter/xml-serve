<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

class extensions_test extends TestCase
{

    public static function setUpBeforeClass(): void
    {
        xml_serve_extensions::init_extensions();
    }

    public function testExtensionList(): void 
    {
        $result = xml_serve_extensions::extenion_list();
        print_r($result);
        $this->assertTrue(is_array($result));
    }
}
