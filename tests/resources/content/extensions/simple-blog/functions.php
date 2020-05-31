<?php

function blog_page($location, $path)
    {
        php_logger::warning("BLOG PAGE: location=$location, path=$path");
        blog_page::$x = 1;
        blog_page::$loc = $location;
        blog_page::$ref = $path;
        $r = xml_serve::xml_content("<pagedef/>")->documentElement;
        php_logger::warning($r);
        return $r;
    }

class blog_page {
    static $x = 0;
    static $loc = "";
    static $ref = "";

}
