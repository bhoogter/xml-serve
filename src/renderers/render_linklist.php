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
        $item_class = $el->getAttribute("item-class");

        $type = $el->getAttribute("type");
        if ($type == '') $type = 'ul';
        $item_type = $el->getAttribute("item-type");
        if ($item_type == '' && ($type=='ul' || $type == 'ol')) $item_type = 'li';

        $type_key = explode(' ', $type)[0];
        $item_type_key = $item_type == '' ? '' : explode(' ', $item_type)[0];
        php_logger::log("CALL id=$id,  type=$type, class=$class, type_key=$type_key, item-type=$item_type, item_class=$item_class");

        $xml = '';
        $xml .= "<$type" . ($class==""?"":" class='$class'>") . ">";

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

            $link = '';
            $link .= "<a ";
            if ($item_class != '') $link .= "class='$class' ";
            $link .= "href='$url' ";
            $link .= ">";
            $link .= $text;
            $link .= "</a>";

            if ($item_type_key != '') $xml .= "<$item_type" . ($item_class==""?"":" class='$item_class'") . ">";
            $xml .= $link;
            if ($item_type_key != '') $xml .= "</$item_type_key>";
        }
        
        $xml .= "</" . $type_key . ">";

        // php_logger::log("\n============= render_linklist: \n");
        // php_logger::log($xml);
        // php_logger::log("\n=============\n");
        $result = xml_serve::xml_content($xml);
        php_logger::trace($result->saveXML());
        return $result;
    }
}