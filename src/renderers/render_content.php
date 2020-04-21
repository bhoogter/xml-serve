<?php

class render_content extends render_base
{
    public static function init()
    {
        xml_serve::add_handler("content", get_class()."::render");
    }

    public static function render($el)
    {
        php_logger::log("CALL");
        $id = $el->getAttribute("id");
        $src = $el->getAttribute("src");
        $type = $el->getAttribute("type");

        if ($src == "") $src = xml_serve::$template->get("/*/content[@id='$id']/@src");
        if ($src == "") $src = "$id.html";

        if ($type == "") $type = xml_serve::$template->get("/*/content[@id='$id']/@type");

        php_logger::log("=========: " . xml_serve::template_name());
        $res = xml_serve::resource_resolver()->resolve_file($src, "templates", xml_serve::template_name());
        if ($type == "") $type = substr($userfile_name, strrpos($userfile_name, '.')+1);
        switch (strtolower($type)) {
            case 'text':
            case 'txt':
                return xml_serve::xml_content(file_get_contents($res));
            case 'xml':
            case 'xhtml':
                return xml_serve::xml_content($res);
            case 'md':
                $cont = file_get_contents($res);
                require_once(__DIR__ . "support/slimdown.php");
                $html = Slimdown::render($cont);
                $xml = xml_file::make_tidy_string($html, "xhtml");
                return xml_serve::xml_content(Slimdown::render($xml));
            default:
                $cont = file_get_contents($res);
                return xml_serve::xml_content($cont);
        }

        return $el;
    }
}
