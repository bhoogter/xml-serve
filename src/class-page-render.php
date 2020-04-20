<?php

class page_render extends page_handlers
{

    protected static $generator = "PAGE_RENDER";

    public static $pagedef;
    public static $template;
    public static $settings;
    public static $page_source;
    
    public $page_result;

    public static function resource_resolver($rr = null)
    {
        if ($rr != null) resource_resolver::$instance = $rr;
        return resource_resolver::instance();
    }

    public static function resolve_ref($resource, $types = [], $mappings = [], $subfolders = ['.', '*']) { return self::resource_resolver()->resolve_ref($resource, $types, $mappings, $subfolders); }
    public static function script_type($filename) { return self::resource_resolver()->script_type($filename); }
    public static function image_format($fn) { return self::resource_resolver()->image_format($fn); }

    public static function pagedef_dom() { if (!self::$pagedef) throw new Exception("No pagedef set."); return self::$pagedef->Doc; }
    public static function template_dom() { if (!self::$template) throw new Exception("No template set."); return self::$template->Doc; }
    public static function settings_dom($xmlfile = null) { 
        if ($xmlfile != null) self::$settings = $xmlfile;
        if (!self::$settings) throw new Exception("Settings DOM not set."); 
        return self::$settings->Doc; 
    }

    public static function generator_name() { return self::$generator; }


    public static function make_page_xsl()
    {
        $filename = __DIR__ . "/stylesheets/make-page.xsl";
        php_logger::debug("filename=$filename", __DIR__, __FILE__);
        return file_get_contents($filename);
    }

    public static function get($path) { return self::$pagedef->get($path); }
    public static function template_name() { return self::get("/pagedef/@template"); }

    public function make_page($pagedef)
    {
        php_logger::log("page_render::make_page()");
        self::$pagedef = $pagedef;
        $template_name = $this->get("/pagedef/@template");
        php_logger::log("page_render::make_page - template_name=$template_name");
        $template_file = $this->resource_resolver()->resolve_file("template.xml", "template", $template_name);
        php_logger::log("page_render::make_page - template_file=$template_file");
        if ($template_file == null) return null;
        self::$template = new xml_file($template_file);
        $this->page_result = new xml_file(xml_file::transformXMLXSL_static($pagedef->saveXML(), $this->make_page_xsl(), true));
        return $this->page_result;
    }
}
