<?php

class page_render {
    public const DEBUG_MAKE_PAGE = "";
    private static $pagedef;
    private static $template;
    private static $settings;
    public static $settings_file;

    private static $handlers;

    protected static function init_handlers() {
        if (is_array(self::$handlers)) return;
        self::$handlers = [];
        self::add_handler("content", "page_render::render_content");
    }

    public static function generator_name() {
        return strtoupper(get_class());
    }

    public static function add_handler($type, $handler) {
        self::init_handlers();
        self::$handlers[$type] = $handler;
    }

    public static function remove_handler($type) {
        self::init_handlers();
        unset(self::$handlers[$type]);
    }

    public static function handler_list() {
        self::init_handlers();
        $result = "," . join(",", array_keys(self::$handlers)) . ",";
        return $result;
    }

    public static function handle_element($type, $El) {
        if (!isset(self::$handlers[$type])) return null;
        $handler = self::$handlers[$type];
        $result = call_user_func($handler, $El);
        return $result;
    }

    public static function make_page_xsl() {
        $d = (strpos(__FILE__, ".phar") === false ? __DIR__ : "phar://" . __FILE__ . "/src");
        $filename = $d . "/stylesheets/make-page.xsl";
        // print "\n<br/>filename=$filename";
        return file_get_contents($filename);
    }

    // public static function include_support() {
    //     $d = (strpos(__FILE__, ".phar") === false ? __DIR__ : "phar://" . __FILE__ . "/src");
    //     require_once($d . "/support/first.php");
    // }

    public static function render_content($El) {
        
        return "";
    }

    public static function get($path) { return self::$pagedef->get($path); }

    public static function template_dom() {
        return self::$template->Doc;
    }

    public static function template_name() {
        return self::$template->Doc;
    }

    public static function site_settings_file() {
        if (self::$settings_file == null || self::$settings_file == "") {
            self::$settings_file = __DIR__ . "/resources/site.xml";
        }
        return self::$settings_file;
    }

    public static function site_settings_dom() {
        if (self::$settings == null) self::$settings = new xml_file(self::site_settings_file());
        return self::$settings->Doc;
    }

    public static function make_page($pagedef) {
        // print "\n<br/>page_render::make_page()";
        self::$pagedef = $pagedef;
        $template_name = self::get("/pagedef/@template");
        // print "\n<br/>page_render::make_page - template_name=$template_name";
        $template_file = resource_resolver::resolve_file("template.xml", "template", $template_name);
        // print "\n<br/>page_render::make_page - template_file=$template_file";
        if ($template_file == null) return null;
        self::$template = new xml_file($template_file);
        $result = new xml_file(xml_file::transformXMLXSL_static($pagedef->saveXML(), self::make_page_xsl(), true));
        return $result;
    }
}
