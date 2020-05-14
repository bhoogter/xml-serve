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
    public static $extension_source;
    
    public static $page_result;

    public static function init($resource_folder = '', $http_root = '', $pagesrc = null, $sitesettings = null, $extension_source = null)
    {
        $resource_folder = realpath($resource_folder);
        $http_root = realpath($http_root);

        php_logger::log("CALL");
        if ($resource_folder != '') self::$resource_folder = $resource_folder;
        else throw new Exception("Missing argument 1: resource_folder (string path)");
        if ($http_root == '') $http_root = $resource_folder;
        self::resource_resolver()->init(realpath($resource_folder), realpath($http_root));

        self::init_handlers();

        if ($pagesrc != null) {
            if (is_object($pagesrc)) self::$page_source = $pagesrc;
            else if (file_exists($l = realpath($pagesrc))) self::$page_source = new xml_file($l);
            else if (file_exists($l = realpath(self::$resource_folder . $pagesrc))) self::$page_source = new xml_file($l);
            else if (file_exists($l = realpath(__DIR__ . "/pages.xml"))) self::$page_source = new xml_file($l);
        }
        if (self::$page_source == null) throw new Exception("Missing argument 3: pages source (filename, xml_file)");

        if ($sitesettings != null) {
            if (is_object($sitesettings)) self::$settings = $sitesettings;
            else if (file_exists($l = realpath($sitesettings))) self::$settings = new xml_file($l);
            else if (file_exists($l = realpath(self::$resource_folder . $sitesettings))) self::$settings = new xml_file($l);
            else if (file_exists($l = realpath(__DIR__ . "/site.xml"))) self::$settings = new xml_file($l);
        }
        if (self::$settings == null) throw new Exception("Missing argument 4: site settings (filename, xml_file)");

        if ($extension_source != null) {
            if (is_object($extension_source)) self::$extension_source = $extension_source;
            else if (file_exists($l = realpath($extension_source))) self::$extension_source = new site_settings($l);
            else if (file_exists($l = realpath(self::$resource_folder . $extension_source))) self::$extension_source = new site_settings($l);
            else if (file_exists($l = realpath(__DIR__ . "/site.xml"))) self::$extension_source = new site_settings($l);
        }
        if (self::$settings == null) throw new Exception("Missing argument 4: site settings (filename, site_settings)");
    }

    public static function resource_resolver($rr = null)
    {
        if ($rr != null) resource_resolver::$instance = $rr;
        return resource_resolver::instance();
    }

    public static function resolve_files($resource, $types = [], $mappings = [], $subfolders = ['.', '*']) { return self::resource_resolver()->resolve_files($resource, $types, $mappings, $subfolders); }
    public static function resolve_refs($resource, $types = [], $mappings = [], $subfolders = ['.', '*']) { return self::resource_resolver()->resolve_refs($resource, $types, $mappings, $subfolders); }
    public static function resolve_file($resource, $types = [], $mappings = [], $subfolders = ['.', '*']) { return self::resource_resolver()->resolve_file($resource, $types, $mappings, $subfolders); }
    public static function resolve_ref($resource, $types = [], $mappings = [], $subfolders = ['.', '*']) { return self::resource_resolver()->resolve_ref($resource, $types, $mappings, $subfolders); }
    public static function content_type($filename) { return self::resource_resolver()->content_type($filename); }

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

    protected static function new_pagepart_xml($element, $pageset, $request)
    {
        php_logger::log("CALL (..., $pageset)");
        $pageset_check = $pageset == '' ? "not(@id)" : "@id='$pageset'";
        // print xml_file::nodeXml($element);
        $xml = xml_file::nodeXmlFile($element);
        $xml->set("/pagedef/@pageset", $pageset);
        $xml->set("/pagedef/@request", $request);
        php_logger::dump($xml->saveXML());

        if (($template = self::$page_source->get("/pages/pageset[$pageset_check]/@template")) != '')
            $xml->set("/pagedef/@template", $template);
        else if (($template = self::$page_source->get("/pages/pageset[@default]/@template")) != '')
            $xml->set("/pagedef/@template", $template);
        return $xml;
    }

    protected static function page_part_element($index, &$pageset = "", &$http_result = 200)
    {
        php_logger::log("CALL ($index, $pageset)");
        $extension_handler = null;

        $pageset_check = $pageset == '' ? "not(@id)" : "@id='$pageset'";
        if (substr($index, 0, 1) == '/') $index = substr($index, 1);
        if (substr($index, -1) == '/') $index = substr($index, 0, strlen($index) - 1);
        php_logger::log("CALL ($index, $pageset)");
        if ((self::$page_source->nde("/pages/pageset[$pageset_check]/pagedef[@loc='$index']"))  != null) {
            $subpageset = self::$page_source->get("/pages/pageset[$pageset_check]/pagedef[@loc='$index']/@pageset");
            php_logger::log("exact match $index (pageset=$pageset)");

            if ($subpageset != null) {
                php_logger::log("matched subset default");
                $pageset = $subpageset;
                return self::page_part_element("", $subpageset);
            }

            $match = "/pages/pageset[$pageset_check]/pagedef[@loc='$index']";
            $result = self::$page_source->nde($match);
            php_logger::debug("Returning match for: $match");
            php_logger::dump("\n", xml_file::nodeXml($result));
            return $result;
        }
        $path = $index;
        $rest = "";
        while (true) {
            if ($path == '') break;
            $x = strrpos($path, '/');
            if (($x = strrpos($path, '/')) == false) {
                $rest = "$path/$rest";
                $path = '';
            } else {
                $rest = substr($path, $x + 1) . ($rest == "" ? "" : "/") . $rest;
                $path = substr($path, 0, $x);
            }
            php_logger::log("Searching path tree: path=$path, rest=$rest");
            php_logger::debug("Searching: /pages/pageset[$pageset_check]/pagedef[@loc='$path']/@pageset");

            $subpageset = self::$page_source->get("/pages/pageset[$pageset_check]/pagedef[@loc='$path']/@pageset");
            if ($subpageset != null) {
                php_logger::trace("subpath pageset $pageset");
                $pageset = $subpageset;
                $subset_result = self::page_part_element($rest, $pageset);
                if ($subset_result != null) return $subset_result;
                php_logger::trace("subpath didn't find.  No 404 handler provided.");
                break;
            }
        }

        $extension_check = self::$page_source->get("/pages/pageset[$pageset_check]/pagedef[@loc='$path']/@extension");
        if ($extension_check) {
            php_logger::trace("extension handler on path [$path]: $extension_check");
            return self::$page_source->nde("/pages/pageset[$pageset_check]/pagedef[@loc='$path']");
        }

        if ($index == "") {
            php_logger::debug("CALL  - Checking default on /pages/pageset[$pageset_check]/pagedef[@default]/@loc");
            if (self::$page_source->get("/pages/pageset[$pageset_check]/pagedef[@default]/@loc") != '') {
                php_logger::trace("CALL  - default match");
                return self::$page_source->nde("/pages/pageset[$pageset_check]/pagedef[@default]");
            }
        } else {
            php_logger::trace("Checking 404 on /pages/pageset[$pageset_check]/pagedef[@default404]/@loc");
            if (self::$page_source->get("/pages/pageset[$pageset_check]/pagedef[@default404]/@loc") != '') {
                php_logger::trace("404 match");
                $http_result = 404;
                return self::$page_source->nde("/pages/pageset[$pageset_check]/pagedef[@default404]");
            }
        }

        $http_result = 404;
        php_logger::log("NO MATCH");
        return null;
    }

    public static function page_part($index, &$http_result = 200)
    {
        php_logger::log("CALL ($index)");
        $pageset = "";
        $element = self::page_part_element($index, $pageset, $http_result);
        php_logger::debug("pageset=$pageset, http_result=$http_result");
        if ($element == null) return null;
        $result = self::new_pagepart_xml($element, $pageset, $index);
        php_logger::dump($result->saveXML());
        return $result;
    }

    public static function make_page_xsl()
    {
        $filename = __DIR__ . "/stylesheets/make-page.xsl";
        php_logger::debug("filename=$filename", __DIR__, __FILE__);
        return file_get_contents($filename);
    }    

    public static function make_page($pagedef)
    {
        self::$pagedef = $pagedef;
        php_logger::log("CALL", self::$pagedef);
        php_logger::dump("PAGEDEF: ",self::$pagedef->saveXML());
        $template_name = self::$pagedef->get("/pagedef/@template");
        php_logger::log("template_name=$template_name");
        $template_file = self::resource_resolver()->resolve_file("template.xml", "template", $template_name);
        if ($template_file == null) {
            php_logger::info("Specified template [$template_name] not found.  Trying default...");
            $defaulttemplate = self::$settings->get("/site/global/defaulttemplate");
            $template_file = self::resource_resolver()->resolve_file("template.xml", "template", $defaulttemplate);
            if (!$template_file) {
                $msg = "Unable to find template.  template=$template_name, defaulttemplate=$defaulttemplate";
                php_logger::error($msg);
                throw new Exception($msg);
            }
            self::$pagedef->set("/pagedef/@template", $defaulttemplate);
        }
        php_logger::log("template_file=$template_file");
        self::$template = new xml_file($template_file);
        php_logger::dump("TEMPLATE", self::$template->saveXML());

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
        if (($ext = $pagedef->get("/@extension"))) {
            $result = xml_serve_extensions::call_extension_handler($ext, "page");
            if ($result != null) {
                $pagedef = xml_file::toXmlFile($result);
                return;
            }
        }
    }

    public static function get_page($index)
    {
        // php_logger::set_log_level(get_class(), "all");
        // php_logger::set_log_level("render_content", "all");
        // php_logger::set_log_level("resource_resolver", "all");
        php_logger::log("CALL ($index)");
        $http_result = 200;
        $pagedef = self::page_part($index, $http_result);
        php_logger::debug("HTTP RESULT: $http_result", "pagedef=".$pagedef->saveXML());
        self::parse_special($pagedef, $http_result);
        $page = self::make_page($pagedef);
        self::$page_result = xml_file::make_tidy_string($page->saveXML());
        php_logger::debug("page result len=".strlen(self::$page_result));
        return self::$page_result;
    }
}
