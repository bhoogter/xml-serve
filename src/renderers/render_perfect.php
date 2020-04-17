<?php

class render_perfect extends render_base
{
    public static function init()
    {
        php_logger::log("CALL");
        page_render::add_handler("a", get_class()."::perfect_a", 50);
        page_render::add_handler("img", get_class()."::perfect_img");
    }

    public static function perfect_url($url) 
    {
        $base_url = page_render::$settings->get("/site/global/url");
        if (substr($url, 0, 1) == '/') $url = $base_url . $url;
        $url = str_replace("//", "/", $url);
        return $url;
    }

    public static function match_url($url) 
    {
        if (strpos($url, '*') !== false && strpos($url, '?') !== false) return $url;
        return $url;
    }

    public static function perfect_a($el) {
        php_logger::log("CALL", $el);
        print $el->tagName;
        $href = $el->getAttribute("href");
        if ($href) $el->setAttribute("href", self::perfect_url($href));
        $target = $el->getAttribute("target");
        $onclick = $el->getAttribute("onMouseDown");
        if ($target != '' && $onclick == '') {
            $el.setAttribute('onMouseDown', 'target=\'$target\'');
            $el.removeAttribute('target');
        }
        return $el;
    }

    public static function perfect_img($el)
    {
        php_logger::log("CALL", $el);
        $src = $el->getAttribute("src");
        $src = self::match_url($src);
        $src = self::perfect_url($src);
        $el->setAttribute("src", $src);

        $alt = $el->getAttribute("alt");
        if ($alt == "") $el->setAttribute("alt", "image");

        return $el;
    }
}
