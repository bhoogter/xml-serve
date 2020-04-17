<?php

class xml_serve extends page_render
{
    private $pages_source;
    private $resource_folder;

    function __construct()
    {
        $n = func_num_args();
        $a = func_get_args();
        if ($n >= 1) {
            $this->resource_folder = $a[0];
        } else {
            throw new Exception("Missing argument 1: resource_folder (string path)");
        }

        if ($n >= 2) {
            if (is_object($a[1])) $this->pages_source = $a[1];
            else if (file_exists($l = realpath($a[1]))) $this->pages_source = new xml_file($l);
            else if (file_exists($l = realpath($this->resource_folder . $a[1]))) $this->pages_source = new xml_file($l);
            else if (file_exists($l = realpath(__DIR__ . "/pages.xml"))) $this->pages_source = new xml_file($l);
        }
        if ($this->pages_source == null) throw new Exception("Missing argument 2: pages source (filename, xml_file)");

        if ($n >= 3) {
            if (is_object($a[2])) $this->settings = $a[2];
            else if (file_exists($l = realpath($a[2]))) self::$settings = new xml_file($l);
            else if (file_exists($l = realpath($this->resource_folder . $a[2]))) self::$settings = new xml_file($l);
            else if (file_exists($l = realpath(__DIR__ . "/site.xml"))) self::$settings = new xml_file($l);
        }
        if (self::$settings == null) throw new Exception("Missing argument 3: site settings (filename, xml_file)");
    }

    function template_folder()          {        return $this->resource_folder;    }
    function pages_source()             {        return $this->pages_source;    }
    function source_part_get($index)    {        return $this->pages_source()->get($index);    }
    function source_part_nde($index)    {        return $this->pages_source()->nde($index);    }
    function source_part_def($index)    {        return $this->pages_source()->def($index);    }

    protected function new_pagepart_xml($element, $pageset)
    {
        php_logger::log("CALL (..., $pageset)");
        $pageset_check = $pageset == '' ? "not(@id)" : "@id='$pageset'";
        // print xml_file::nodeXml($element);
        $xml = xml_file::nodeXmlFile($element);
        $xml->set("/pagedef/@pageset", $pageset);
        if (($template = $this->source_part_get("/pages/pageset[$pageset_check]/@template")) != '')
            $xml->set("/pagedef/@template", $template);
        else if (($template = $this->source_part_get("/pages/pageset[@default]/@template")) != '')
            $xml->set("/pagedef/@template", $template);
        return $xml;
    }

    protected function page_part_element($index, &$pageset = "", &$http_result = 200)
    {
        php_logger::log("CALL ($index, $pageset)");
        $pageset_check = $pageset == '' ? "not(@id)" : "@id='$pageset'";
        if (substr($index, 0, 1) == '/') $index = substr($index, 1);
        if (substr($index, -1) == '/') $index = substr($index, 0, strlen($index) - 1);
        php_logger::log("CALL ($index, $pageset)");
        if (($this->source_part_nde("/pages/pageset[$pageset_check]/pagedef[@loc='$index']"))  != null) {
            $subpageset = $this->source_part_get("/pages/pageset[$pageset_check]/pagedef[@loc='$index']/@pageset");
            php_logger::log("exact match $index (pageset=$pageset)");

            if ($subpageset != null) {
                $pageset = $subpageset;
                return $this->page_part_element("", $subpageset);
            }
            return $this->source_part_nde("/pages/pageset[$pageset_check]/pagedef[@loc='$index']");
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

            $subpageset = $this->source_part_get("/pages/pageset[$pageset_check]/pagedef[@loc='$path']/@pageset");
            if ($subpageset != null) {
                php_logger::trace("subpath pageset $pageset");
                $pageset = $subpageset;
                $subset_result = $this->page_part_element($rest, $pageset);
                if ($subset_result != null) return $subset_result;
                php_logger::trace("subpath didn't find.  No 404 handler provided.");
                break;
            }
        }

        if ($index == "") {
            php_logger::debug("CALL  - Checking default on /pages/pageset[$pageset_check]/pagedef[@default]/@loc");
            if ($this->source_part_get("/pages/pageset[$pageset_check]/pagedef[@default]/@loc") != '') {
                php_logger::trace("CALL  - default match");
                return $this->source_part_nde("/pages/pageset[$pageset_check]/pagedef[@default]");
            }
        } else {
            php_logger::trace("Checking 404 on /pages/pageset[$pageset_check]/pagedef[@default404]/@loc");
            if ($this->source_part_get("/pages/pageset[$pageset_check]/pagedef[@default404]/@loc") != '') {
                php_logger::trace("404 match");
                $http_result = 404;
                return $this->source_part_nde("/pages/pageset[$pageset_check]/pagedef[@default404]");
            }
        }

        $http_result = 404;
        php_logger::log("NO MATCH");
        return null;
    }

    function page_part($index, &$http_result = 200)
    {
        php_logger::log("CALL ($index)");
        $pageset = "";
        $element = $this->page_part_element($index, $pageset, $http_result);
        php_logger::debug("pageset=$pageset, http_result=$http_result");
        if ($element == null) return null;
        return $this->new_pagepart_xml($element, $pageset);
    }

    function parse_special($pagedef, $http_result = 200)
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

    function get_page($index)
    {
        php_logger::log("CALL ($index)");
        $http_result = 200;
        $pagedef = $this->page_part($index, $http_result);
        php_logger::debug("HTTP RESULT: $http_result");
        $this->parse_special($pagedef, $http_result);
        $page = page_render::make_page($pagedef);
        $result = xml_file::make_tidy_string($page->saveXML());
        return $result;
    }
}
