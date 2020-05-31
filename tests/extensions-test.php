<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

class extensions_test extends TestCase
{

    public function testExtensionList(): void 
    {
        $result = xml_serve_extensions::extension_list();
        print_r($result);
        $this->assertTrue(is_array($result));
    }
}
