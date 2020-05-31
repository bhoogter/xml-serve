<?php

class xml_serve_extensions
{
    private static $extensions;

    private static function init_extensions()
    {
        if (!is_array(self::$extensions)) self::$extensions = [];
    }

    public static function extension_list()
    { 
        self::init_extensions();
        return array_keys(self::$extensions); 
    }

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

    public static function add_extension_handler($name, $type, $handler)
    {
        self::init_extensions();
        if (!array_key_exists($name, self::$extensions)) self::$extensions[$name] = [];
        self::$extensions[$name][$type] = $handler;
    }

    public static function remove_extension_handler($name, $type)
    {
        self::init_extensions();
        if (!array_key_exists($name, self::$extensions)) self::$extensions[$name] = [];
        unset(self::$extensions[$type]);
    }

    public static function get_extension_handler($name, $type)
    {
        self::init_extensions();
        if (!array_key_exists($name, self::$extensions)) return null;
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
