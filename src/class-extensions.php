<?php

class xml_serve_extensions
{
    private static $extensions;

    public static function init_extensions()
    {
        if (!is_array(self::$extensions)) self::$extensions = [];
    }

    public static function extenion_list()
    { return array_keys(self::$extensions); }

    public static function add_extension($name, $handlers)
    {
        self::init_extensions();
        self::$extensions[$name] = $handlers;
    }

    public static function remove_extension($name) 
    {
        self::init_extensions();
        unset(self::$extensions[$name]);
    }

    public static function get_extension_handler($name, $type)
    {
        self::init_extensions();
        if (!isset(self::$extensions[$name])) return null;
        $handlers = self::$extensions[$name];
        return @$handlers[$type];
    }

    public static function call_extension_handler($loc, $path, $name, $type)
    {
        $handler = self::get_extension_handler($name, $type);
        if (!$handler) return null;
        if (!is_callable($handler))
            throw new Exception("Handler [$handler] is not callable.");
        $result = call_user_func($handler, $loc, $path);

        return $result;
    }
}
