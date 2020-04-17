<?php

class page_render
{
    public const DEBUG_MAKE_PAGE = "";

    protected static $pagedef;
    protected static $template;
    protected static $settings;

    private $handlers;

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
    public static function settings_dom() { if (!self::$settings) throw new Exception("Settings DOM not set."); return self::$settings->Doc; }

    public function generator_name() { return strtoupper(get_class()); }

    protected function init_handlers()
    {
        if (is_array($this->handlers)) return;
        $this->include_support();
        $this->handlers = [];
        $this->add_handler("content", "render_content::render");
    }


    public function add_handler($type, $handler)
    {
        $this->init_handlers();
        $this->handlers[$type] = $handler;
    }

    public function remove_handler($type)
    {
        $this->init_handlers();
        unset($this->handlers[$type]);
    }

    public function handler_list()
    {
        $this->init_handlers();
        $result = "," . join(",", array_keys($this->handlers)) . ",";
        return $result;
    }

    public function handle_element($type, $El)
    {
        if (!isset($this->handlers[$type])) return null;
        $handler = $this->handlers[$type];
        $result = call_user_func($handler, $El);
        return $result;
    }

    public function make_page_xsl()
    {
        $filename = __DIR__ . "/stylesheets/make-page.xsl";
        php_logger::debug("filename=$filename", __DIR__, __FILE__);
        return file_get_contents($filename);
    }

    public function include_support()
    {
        php_logger::trace(__DIR__);
        require_once(__DIR__ . "/renderers/render_base.php");
        require_once(__DIR__ . "/renderers/render_content.php");
    }

    public function get($path)
    {
        return $this->pagedef->get($path);
    }

    public function template_name()
    {
        return $this->pagedef->get("/pagedef/@template");
    }

    public function make_page($pagedef)
    {
        php_logger::log("page_render::make_page()");
        self::$pagedef = $pagedef;
        $template_name = $this->get("/pagedef/@template");
        php_logger::log("page_render::make_page - template_name=$template_name");
        $template_file = $this->resource_resolver()->resolve_file("template.xml", "template", $template_name);
        php_logger::log("page_render::make_page - template_file=$template_file");
        if ($template_file == null) return null;
        $this->template = new xml_file($template_file);
        $result = new xml_file(xml_file::transformXMLXSL_static($pagedef->saveXML(), $this->make_page_xsl(), true));
        return $result;
    }
}
