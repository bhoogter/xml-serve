<?php

function blog_page($path, $location, $method, $url)
    {
        php_logger::warning("BLOG PAGE: location=$location, path=$path");
        blog_page::set($path, $location, $method, $url);
        $s = "<pagedef>";
        $s .= " <content id='content' type='xhtml' src='simple-blog-content.xml' />";
        $s .= "</pagedef>";

        $r = xml_serve::xml_content("<pagedef/>")->documentElement;
        php_logger::warning($r);
        return $r;
    }

class blog_page {
    static $x = 0;
    static $path = "";
    static $loc = "";
    static $method = "";
    static $url = "";

    public static function set($path, $location, $method, $url, $x = 1) {
        self::$x = $x;
        self::$path = $path;
        self::$loc = $location;
        self::$method = $method;
        self::$url = $url;
    }

    public static function reset() {
        self::set("", "", "", "", 0);
    }
}
