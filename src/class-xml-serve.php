<?php

class xml_serve extends page_handlers
{
    public const DEBUG_MAKE_PAGE = "";
    public static $generator = "XML_SERVE";

    public static $resource_folder;

    public static $pagedef;
    public static $template;
    public static $settings;
    public static $page_source;
    
    public static $page_result;

    public static function init($resource_folder = '', $pagesrc = null, $sitesettings = null)
    {
        php_logger::log("CALL");
        if ($resource_folder != '') {
            self::$resource_folder = $resource_folder;
            self::resource_resolver()->init($resource_folder);
        } else {
            throw new Exception("Missing argument 1: resource_folder (string path)");
        }

        if ($pagesrc != null) {
            if (is_object($pagesrc)) self::$page_source = $pagesrc;
            else if (file_exists($l = realpath($pagesrc))) self::$page_source = new page_source($l);
            else if (file_exists($l = realpath(self::$resource_folder . $pagesrc))) self::$page_source = new page_source($l);
            else if (file_exists($l = realpath(__DIR__ . "/pages.xml"))) self::$page_source = new page_source($l);
        }
        if (self::$page_source == null) throw new Exception("Missing argument 2: pages source (filename, xml_file)");

        if ($sitesettings != null) {
            if (is_object($sitesettings)) self::$settings = $sitesettings;
            else if (file_exists($l = realpath($sitesettings))) self::$settings = new xml_file($l);
            else if (file_exists($l = realpath(self::$resource_folder . $sitesettings))) self::$settings = new xml_file($l);
            else if (file_exists($l = realpath(__DIR__ . "/site.xml"))) self::$settings = new xml_file($l);
        }
        if (self::$settings == null) throw new Exception("Missing argument 3: site settings (filename, xml_file)");
    }

    public static function resource_resolver($rr = null)
    {
        if ($rr != null) resource_resolver::$instance = $rr;
        return resource_resolver::instance();
    }

    public static function resolve_ref($resource, $types = [], $mappings = [], $subfolders = ['.', '*']) { return self::resource_resolver()->resolve_ref($resource, $types, $mappings, $subfolders); }
    public static function script_type($filename) { return self::resource_resolver()->script_type($filename); }
    public static function image_format($fn) { return self::resource_resolver()->image_format($fn); }

    public static function template_folder()          {        return self::resource_folder;    }
    public static function page_source()              {        return self::$page_source;    }

    public static function pagedef_dom() { if (!self::$pagedef) throw new Exception("No pagedef set."); return self::$pagedef->Doc; }
    public static function template_dom() { if (!self::$template) throw new Exception("No template set."); return self::$template->Doc; }
    public static function settings_dom($xmlfile = null) { 
        if ($xmlfile != null) self::$settings = $xmlfile;
        if (!self::$settings) throw new Exception("Settings DOM not set."); 
        return self::$settings->Doc; 
    }

    public static function template_name() { return self::$pagedef->get("/pagedef/@template"); }
    public static function generator_name() { return self::$generator; }

    public static function make_page_xsl()
    {
        $filename = __DIR__ . "/stylesheets/make-page.xsl";
        php_logger::debug("filename=$filename", __DIR__, __FILE__);
        return file_get_contents($filename);
    }    

    public function make_page($pagedef)
    {
        php_logger::log("page_render::make_page()");
        self::$pagedef = $pagedef;
        $template_name = self::$pagedef->get("/pagedef/@template");
        php_logger::log("page_render::make_page - template_name=$template_name");
        $template_file = self::resource_resolver()->resolve_file("template.xml", "template", $template_name);
        php_logger::log("page_render::make_page - template_file=$template_file");
        if ($template_file == null) return null;
        self::$template = new xml_file($template_file);
        self::$page_result = new xml_file(xml_file::transformXMLXSL_static($pagedef->saveXML(), self::make_page_xsl(), true));
        return self::$page_result;
    }

    public static function parse_special($pagedef, $http_result = 200)
    {
        php_logger::log("CALL (..., $http_result)", $pagedef);
        http_response_code($http_result);
        // 301 Moved Permanently, 302 Found, 303 See Other, 307 Temporary Redirect
        if ($pagedef == null) {
            http_response_code(404);
            // include('404.php');
            print "404 Not Found";
            die();
        }
        if (($url = $pagedef->get("/@redirect")) != '') {
            $type = $pagedef->get("/@redirect-type");
            if ($type == '') $type = 301;
            die(header("Location: $url", TRUE, $type));
        }
    }

    public static function get_page($index)
    {
        php_logger::log("CALL ($index)");
        $http_result = 200;
        self::$pagedef = self::$page_source->page_part($index, $http_result);
        php_logger::debug("HTTP RESULT: $http_result");
        self::parse_special(self::$pagedef, $http_result);
        $page = self::make_page(self::$pagedef);
        $result = xml_file::make_tidy_string($page->saveXML());
        return $result;
    }
}
