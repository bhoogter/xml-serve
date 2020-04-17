<?php

class page_render
{
    public const DEBUG_MAKE_PAGE = "";

    protected static $generator = "PAGE_RENDER";

    public static $pagedef;
    public static $template;
    public static $settings;
    
    public $page_result;

    private static $handlers;

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

    public static function xml_content($xml) { return (new xml_file($xml))->Doc; }
    public static function empty_content() { return self::xml_content("<?xml version='1.0'?><div /"); }

    public static function generator_name() { return self::$generator; }


    protected static function init_handlers()
    {
        if (is_array(self::$handlers)) return;
        self::$handlers = [];

        require_once(__DIR__ . "/renderers/render_base.php");
        php_logger::trace("CALL");
        $support = array(
            __DIR__ . "/renderers/render_content.php",
            __DIR__ . "/renderers/render_perfect.php",
            __DIR__ . "/renderers/render_linklist.php",
        );
        foreach($support as $s) {
            require_once($s);
            $class = basename($s, ".php");
            $target ="$class::init";
            php_logger::dump("class=$class, target=$target");
            call_user_func($target);
        }
    }

    public static function add_handler($type, $handler, $priority = 0)
    {
        php_logger::debug("CALL ($type, $handler, $priority)");
        self::init_handlers();
        if (!isset(self::$handlers[$type]))
          self::$handlers[$type] = [];
        while (isset(self::$handlers[$type][$priority])) $priority++;
        self::$handlers[$type][$priority] = $handler;
        return $priority;
    }

    public static function get_handlers($type)
    {
        self::init_handlers();
        if (!isset(self::$handlers[$type])) return [];
        return self::$handlers[$type];
    }

    public static function set_handlers($type, $handlers = [])
    {
        self::init_handlers();
        self::$handlers[$type] = $handlers;
    }    

    public static function remove_handler($type, $idx)
    {
        self::init_handlers();
        if (isset(self::$handlers[$type][$idx]))
            unset(self::$handlers[$type][$idx]);
    }

    public static function handler_list()
    {
        self::init_handlers();
        $result = "," . join(",", array_keys(self::$handlers)) . ",";
        return $result;
    }

    public static function handle_element($type, $el)
    {
        if (!isset(self::$handlers[$type])) return null;
        $handlers = self::$handlers[$type];
        $result = $el;
        if (is_array($result) && sizeof($result) == 1) $result = $result[0];
        foreach($handlers as $h) {
            $result = call_user_func($h, $result);
            if ($result == null) break;
        }   
        if ($result == null) $result = self::empty_content();
        return $result;
    }

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
