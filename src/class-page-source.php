<?php

class page_source extends xml_file
{
    protected  function new_pagepart_xml($element, $pageset, $request)
    {
        php_logger::log("CALL (..., $pageset)");
        $pageset_check = $pageset == '' ? "not(@id)" : "@id='$pageset'";
        // print xml_file::nodeXml($element);
        $xml = xml_file::nodeXmlFile($element);
        $xml->set("/pagedef/@pageset", $pageset);
        $xml->set("/pagedef/@request", $request);
        php_logger::dump($xml->saveXML());

        if (($template = $this->get("/pages/pageset[$pageset_check]/@template")) != '')
            $xml->set("/pagedef/@template", $template);
        else if (($template = $this->get("/pages/pageset[@default]/@template")) != '')
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
        if (($this->nde("/pages/pageset[$pageset_check]/pagedef[@loc='$index']"))  != null) {
            $subpageset = $this->get("/pages/pageset[$pageset_check]/pagedef[@loc='$index']/@pageset");
            php_logger::log("exact match $index (pageset=$pageset)");

            if ($subpageset != null) {
                php_logger::log("matched subset default");
                $pageset = $subpageset;
                return $this->page_part_element("", $subpageset);
            }

            $match = "/pages/pageset[$pageset_check]/pagedef[@loc='$index']";
            $result = $this->nde($match);
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

            $subpageset = $this->get("/pages/pageset[$pageset_check]/pagedef[@loc='$path']/@pageset");
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
            if ($this->get("/pages/pageset[$pageset_check]/pagedef[@default]/@loc") != '') {
                php_logger::trace("CALL  - default match");
                return $this->nde("/pages/pageset[$pageset_check]/pagedef[@default]");
            }
        } else {
            php_logger::trace("Checking 404 on /pages/pageset[$pageset_check]/pagedef[@default404]/@loc");
            if ($this->get("/pages/pageset[$pageset_check]/pagedef[@default404]/@loc") != '') {
                php_logger::trace("404 match");
                $http_result = 404;
                return $this->nde("/pages/pageset[$pageset_check]/pagedef[@default404]");
            }
        }

        $http_result = 404;
        php_logger::log("NO MATCH");
        return null;
    }

    public function page_part($index, &$http_result = 200)
    {
        php_logger::log("CALL ($index)");
        $pageset = "";
        $element = $this->page_part_element($index, $pageset, $http_result);
        php_logger::debug("pageset=$pageset, http_result=$http_result");
        if ($element == null) return null;
        $result = $this->new_pagepart_xml($element, $pageset, $index);
        php_logger::dump($result->saveXML());
        return $result;
    }
}
