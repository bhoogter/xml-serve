<?php

class make_page_render_content {
    public static function xml_content($xml) {
        return (new xml_file($xml))->Doc;
    }

    public static function empty_content() {
        return self::xml_content("<?xml version='1.0'?><div /");
    }

    public static function cdata_content($content) {
        $content = str_replace(']]>', ']]&lt;', $content);
        $xml = "<?xml version='1.0'?><![CDATA[$content]]>";
        return self::xml_content($xml);
    }

    public static function render_content($El) {
        $f = xml_file::nodeXmlFile($El[0]);
        $id = $f->get("@id");

        $src = $f->get("@src");
        if ($src == "") $src = page_render::$template->get("/*/content[@id='$id']/@src");
        if ($src == "") return self::empty_content();

        $type = $f->get("@type");
        if ($type == "") $type = page_render::$template->get("/*/content[@id='$id']/@type");
        if ($type == "") $type = "xhtml";

        // print("\n=========: " . page_render::template_name());
        $res = resource_resolver::resolve_file($src, "templates", page_render::template_name());
        switch($type) {
            case 'text': case 'txt': return self::xml_content(file_get_contents($res));
            case 'xml': case 'xhtml':  return self::xml_content($res);
            default:   return self::xml_content($res);
        }

        return $f->Doc;
    }
}
