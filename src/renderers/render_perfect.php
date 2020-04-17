<?php

class render_perfect extends render_base
{
    public static function init()
    {
        page_render::add_handler("a", "{get_class()}::perfect_a");
        page_render::add_handler("img", "{get_class()}::perfect_img");
    }

    public static function perfect_url($url) 
    {
        $base_url = xml_serve::nodeXmlFile(page_render::settings_dom())->get("/site/global/url");
        if (substr($url, 0, 1) == '/') $url = $base_url . $url;
        $url = str_replace("//", "/", $url);
        return $url;
    }

    public static function match_url($url) 
    {
        return $url;
    }

    public static function perfect_a($el) {
        $href = $el.getAttribute("href");
        if ($href) $el.setAttribute("href", self::perfect_url($href));
        $target = $el.getAttribute("target");
        $onclick = $el.getAttribute("onclick");
        if ($target != '' && $onclick == '') {
            $el.setAttribute('onclick', "target='$target'");
            $el.removeAttribute('target');
        }
        return $el;
    }

    public static function perfect_img($el)
    {
        $src = $el.getAttribute("src");
        if (strpos($src, array('*', '?'))) $src = match_url($src);
        $src = self::perfect_url($src);
        $el.setAttribute("src", $src);

        $alt = $el.getAttribute("alt");
        if ($alt == "") $el.setAttribute("alt", "image");

        return $el;
    }
}
