<?php

class xml_pages
{
    private $pages_source;
    private $resource_folder;

    function __construct()
    {
        $n = func_num_args();
        $a = func_get_args();
        if ($n >= 1) {
            if (is_object($n)) $this->pages_source = $a[0];
            else $this->pages_source = new xml_file($a[0]);
        }
        if ($n >= 2) {
            $this->resource_folder = $a[1];
        }
    }

    function pages_source()
    {
        return $this->pages_source;
    }

    function template_folder()
    {
        return $this->resource_folder;
    }

    function source_part_get($index)
    {
        return $this->pages_source()->get($index);
    }

    function source_part_nde($index)
    {
        return $this->pages_source()->nde($index);
    }

    function source_part_def($index)
    {
        return $this->pages_source()->def($index);
    }

    function page_part($index, $pageset = "")
    {
        $pageset_check = $pageset == '' ? "not(@id)" : "@id='$pageset'";
        // print "\n<br/>xml-pages::page_part($index, $pageset)";
        if (substr($index, 0, 1) == '/') $index = substr($index, 1);
        if (substr($index, -1) == '/') $index = substr($index, 0, strlen($index) - 1);
        //print "\n<br/>xml-pages::page_part($index, $pageset)";
        if (($this->source_part_nde("/pages/pageset[$pageset_check]/pagedef[@loc='$index']"))  != null) {
            $pageset = $this->source_part_get("/pages/pageset[$pageset_check]/pagedef[@loc='$index']/@pageset");
            // print "\n<br/>xml-pages::page_part - exact match $index (pageset=$pageset)";

            if ($pageset != null) return $this->page_part("", $pageset);
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
            // print "\n<br/>xml-pages::page_part - Searching path tree: path=$path, rest=$rest";
            // print "\n<br/>xml-pages::page_part - Searching: /pages/pageset[$pageset_check]/pagedef[@loc='$path']/@pageset";

            $pageset = $this->source_part_get("/pages/pageset[$pageset_check]/pagedef[@loc='$path']/@pageset");
            if ($pageset != null) {
                // print "\n<br/>xml-pages::page_part - subpath pageset $pageset";
                return $this->page_part($rest, $pageset);
            }
        }

        if ($index == "") {
            // print "\n<br/>xml-pages::page_part - Checking default on /pages/pageset[$pageset_check]/pagedef[@default]/@loc";
            if ($this->source_part_get("/pages/pageset[$pageset_check]/pagedef[@default]/@loc") != '') {
                // print "\n<br/>xml-pages::page_part - default match";
                return $this->source_part_nde("/pages/pageset[$pageset_check]/pagedef[@default]");
            }
        } else {
            // print "\n<br/>xml-pages::page_part - Checking 404 on /pages/pageset[$pageset_check]/pagedef[@default404]/@loc";
            if ($this->source_part_get("/pages/pageset[$pageset_check]/pagedef[@default404]/@loc") != '') {
                // print "\n<br/>xml-pages::page_part - 404 match";
                return $this->source_part_nde("/pages/pageset[$pageset_check]/pagedef[@default404]");
            }
        }

        // print "\n<br/>xml-pages::page_part - NO MATCH";
        return null;
    }

    function get_page($index)
    {
        // Exact matches
        if (($id = $this->pages_source()->nde("/pages/page[@loc='$index']/@id")) != null) return $id;
        if (($id = $this->pages_source()->nde("/pages/page[@default='1']/@id")) != null) return $id;

        return null;
    }

    function get_source()
    {
        $id = $this->get_page_id();
        return "";
    }
}
