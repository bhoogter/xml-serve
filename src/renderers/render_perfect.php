<?php

class render_perfect extends render_base
{
    public static function init()
    {
        php_logger::log("CALL");
        xml_serve::add_handler("a", get_class()."::perfect_a", 100);
        xml_serve::add_handler("img", get_class()."::perfect_img", 100);
    }

    public static function perfect_url($url) 
    {
        $base_url = xml_serve::$settings->get("/site/global/url");
        if (substr($url, 0, 1) == '/') $url = $base_url . '/' . $url;
        $url = str_replace("//", "/", $url);
        return $url;
    }

    public static function match_url($url) 
    {
        php_logger::log("url=$url");
        if (strpos($url, '*') !== false || strpos($url, '?') !== false) {
            php_logger::debug("Has Token");
            php_logger::debug("test=$url");
            $files = xml_serve::resolve_refs($url, "templates", xml_serve::template_name());
            php_logger::dump($files);
            if (sizeof($files) > 0) {
                $result = $files[array_rand($files)];
                $result = str_replace(xml_serve::$resource_folder, '', $result);
                php_logger::trace("Modified: result=$result");
                return $result;
            }
        }

        php_logger::debug("No change");
        return $url;  // If we can't improve it, don't make it worse.
    }

    public static function perfect_a($el) {
        php_logger::log("CALL", $el);
        
        $href = $el->getAttribute("href");
        if ($href) $el->setAttribute("href", self::perfect_url($href));
        $target = $el->getAttribute("target");
        $onclick = $el->getAttribute("onMouseDown");
        if ($target != '' && $onclick == '') {
            $el->setAttribute('onMouseDown', 'target=\'$target\'');
            $el->removeAttribute('target');
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
