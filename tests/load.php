<?php

require_once(__DIR__ . "/../src/stub.php");
xml_serve::init(
    __DIR__ . "/resources/content",
    new page_source(__DIR__ . "/resources/pages.xml"),
    new xml_file(__DIR__ . "/resources/site.xml")
);
