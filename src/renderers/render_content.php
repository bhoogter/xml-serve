<?php

class render_content
{
    public static function render($El)
    {
        $f = xml_file::nodeXmlFile($El[0]);
        php_logger::log("CALL", $f->saveXML());
        $id = $f->get("@id");

        $src = $f->get("@src");
        if ($src == "") $src = page_render::$template->get("/*/content[@id='$id']/@src");
        if ($src == "") return page_render::empty_content();

        $type = $f->get("@type");
        if ($type == "") $type = page_render::$template->get("/*/content[@id='$id']/@type");
        if ($type == "") $type = "xhtml";

        php_logger::log("=========: " . page_render::template_name());
        $res = page_render::resource_resolver()->resolve_file($src, "templates", page_render::template_name());
        switch ($type) {
            case 'text':
            case 'txt':
                return page_render::xml_content(file_get_contents($res));
            case 'xml':
            case 'xhtml':
                return page_render::xml_content($res);
            default:
                return page_render::xml_content($res);
        }

        return $f->Doc;
    }
}
