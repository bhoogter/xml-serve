<?php

require_once(__DIR__ . "/../src/stub.php");
xml_serve::init(
    realpath(__DIR__ . "/resources/content"),
    realpath(__DIR__),
    new page_source(__DIR__ . "/resources/pages.xml"),
    new site_settings(__DIR__ . "/resources/site.xml")
);
