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
        $id = $el->getAttribute("id");
        $class = $el->getAttribute("class");
        $style = $el->getAttribute("style");
        php_logger::log("CALL id=$id, class=$class style=$style");

        $xml = '';
        if ($style != '') $xml .= "<$style>";
        else $xml .= '<div>';

        $pageset = xml_serve::$template->get("/pagedef/@pageset");
        $request = xml_serve::$template->get("/pagedef/@request");

        $path = "/pages/pageset";
        $path .= $pageset == '' ? "[not(@id)]" : "[@id='$pageset']" ;
        $path .= "/pagedef[contains(@linklists, '$id')]";
        $links = xml_serve::$page_source->nds($path);
        php_logger::debug("path=$path, n=".sizeof($links));
        php_logger::dump($links);
        foreach($links as $l) {
            $href = $l->getAttribute("href");
            
            if ($href != "") $url = $href;
            else {
                $loc = $l->getAttribute("loc");
                $url = substr($request, 0, strrpos($request, '/') + 1);
                $url .= $loc;
            }
            $text = $l->getAttribute("text");
            if ($text == '') $text = $url;
            $xml .= "<a ";
            if ($class != '') $xml .= "class='$class' ";
            $xml .= "href='$url' ";
            $xml .= ">";
            $xml .= $text;
            $xml .= "</a>";
        }
        if ($style != '') $xml .= "</" . explode(' ', $style)[0]. ">";
        else $xml .= "</div>";

        // php_logger::log("\n============= render_linklist: \n");
        // php_logger::log($xml);
        // php_logger::log("\n=============\n");
        $result = xml_serve::xml_content($xml);
        php_logger::trace($result->saveXML());
        return $result;
    }
}