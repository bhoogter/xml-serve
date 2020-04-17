<?php

class render_content extends render_base
{
    public static function init()
    {
        page_render::add_handler("content", get_class()."::render");
    }

    public static function render($el)
    {
        php_logger::log("CALL");
        $id = $el->getAttribute("id");
        $src = $el->getAttribute("src");
        $type = $el->getAttribute("type");

        if ($src == "") $src = page_render::$template->get("/*/content[@id='$id']/@src");
        if ($src == "") return page_render::empty_content();

        if ($type == "") $type = page_render::$template->get("/*/content[@id='$id']/@type");

        php_logger::log("=========: " . page_render::template_name());
        $res = page_render::resource_resolver()->resolve_file($src, "templates", page_render::template_name());
        if ($type == "") $type = substr($userfile_name, strrpos($userfile_name, '.')+1);
        switch (strtolower($type)) {
            case 'text':
            case 'txt':
                return page_render::xml_content(file_get_contents($res));
            case 'xml':
            case 'xhtml':
                return page_render::xml_content($res);
            case 'md':
                $cont = file_get_contents($res);
                require_once(__DIR__ . "support/slimdown.php");
                $html = Slimdown::render($cont);
                $xml = xml_file::make_tidy_string($html, "xhtml");
                return page_render::xml_content(Slimdown::render($xml));
            default:
                return page_render::xml_content($res);
        }

        return $el;
    }
}
