<?php

class render_linklist extends render_base
{
    public static function init()
    {
        php_logger::log("CALL");
        xml_serve::add_handler("linklist", get_class()."::render");
    }

    public static function render($el) 
    {
        php_logger::log("CALL");
        $id = $el->getAttribute("id");
        $class = $el->getAttribute("class");
        $style = $el->getAttribute("style");

        $xml = '';
        if ($style != '') $xml .= "<$style>";

        $pageset = xml_serve::$template->get("/pagedef/@pageset");
        $request = xml_serve::$template->get("/pagedef/@request");

        $path = "/pages/pageset";
        $path .= $pageset == '' ? "[not(@id)]" : "[@id='$pageset']" ;
        $path .= "/pagedef[contains(@linklists, '$id')]";
        print $links = xml_serve::$pagedef->saveXML();
        $links = xml_serve::$pagedef->nds($path);
        php_logger::debug("path=$path, n=".sizeof($links));
        foreach($links as $l) {
            $href = $l->getAttribute("href");
            if ($href != "") $url = $href;
            else {
                $loc = $l->getAttribute("loc");
                $url = substr($request, 0, strrpos($request, '/') + 1);
                $url .= $loc;
            }
            $xml .= "<a ";
            if ($class != '') $xml .= "class='$class' ";
            $xml .= "href='$url' ";
            $xml .= "/>";
        }
        if ($style != '') $xml .= "</" . explode(' ', $style)[0]. ">";
print $xml;
die();
        return page_render::xml_content($xml);
    }
}