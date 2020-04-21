<?php

class page_handlers
{
    private static $handlers;

    // public static function xml_content($xml) { return (new xml_file($xml))->Doc; }
    public static function xml_content($xml)
    {        return xml_file::XMLToDoc($xml);    }
    public static function empty_content()
    {        return self::xml_content("<?xml version='1.0'?><div />");    }

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
        foreach ($support as $s) {
            require_once($s);
            $class = basename($s, ".php");
            $target = "$class::init";
            php_logger::dump("class=$class, target=$target");
            call_user_func($target);
        }
    }

    public static function reset_handlers() 
    {
        self::$handlers = null;
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
        if (!isset(self::$handlers[$type])) return false;

        if (is_integer($idx)) {
            if (isset(self::$handlers[$type][$idx])) {
                unset(self::$handlers[$type][$idx]);
                return true;
            }
        } else if (is_string($idx)) {
            foreach (self::$handlers[$type] as $k => $v) {
                if ($v == $idx) {
                    unset(self::$handlers[$type][$k]);
                    return true;
                }
            }
        }

        return false;
    }

    public static function handler_list()
    {
        self::init_handlers();
        $result = "," . join(",", array_keys(self::$handlers)) . ",";
        return $result;
    }

    public static function handle_element($type, $el)
    {
        php_logger::trace("type=$type");
        if (!isset(self::$handlers[$type])) {
            php_logger::trace("Unknown handler: $type");
            return $el;
        }
        $handlers = self::$handlers[$type];
        ksort($handlers);
        $result = $el;
        if (is_array($result) && sizeof($result) == 1) $result = $result[0];
        foreach ($handlers as $h) {
            $result = call_user_func($h, $result);
            if ($result == null) {
                php_logger::trace("Got null in handler.  Returning empty content.");
                break;
            }
            if (get_class($result) == "DOMDocument") $result = $result->documentElement;
        }
        if ($result == null) $result = self::empty_content();
        php_logger::trace("Returning NON EMPTY for $type");
        return $result;
    }
}
