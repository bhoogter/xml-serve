<?php

require_once(__DIR__ . "/../../src/stub.php");
xml_serve::init(
    __DIR__ . "/content",
    __DIR__,
    new page_source(__DIR__ . "/pages.xml"),
    new site_settings(__DIR__ . "/site.xml")
);
