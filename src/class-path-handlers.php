<?php

class xml_path_handlers
{
    private static $paths;

    protected static function init_paths()
    {
        if (!is_array(self::$paths)) self::$paths = [];
    }

    public static function clear()
    {
        self::$paths = [];
    }

    public static function path_handler_list()
    {
        self::init_paths();
        return array_keys(self::$paths);
    }

    public static function add($path, $method, $handler)
    {
        self::init_paths();
        if (!array_key_exists($path, self::$paths)) self::$paths[$path] = [];
        self::$paths[$path][$method] = $handler;
    }

    public static function remove($path, $method)
    {
        self::init_paths();
        if (!array_key_exists($path, self::$paths)) return;
        unset(self::$paths[$path][$method]);
    }

    public static function get($path, $method)
    {
        self::init_paths();
        if (!isset(self::$paths[$path])) return null;
        if (!array_key_exists($method, self::$paths[$path])) return null;
        return self::$paths[$path][$method];
    }

    public static function handler_pattern($h, &$keylist) {
        $max = 50;
        $n = 0;
        if (!is_array($keylist)) $keylist = [];
        while(true) {
            if (++$n > $max) break;
            $a = strpos($h, '{');
            if (false === $a) break;
            $b = strpos($h, '}');
            $key = substr($h, $a + 1, $b - $a - 1);
            $h = str_replace('{'.$key.'}', '([^/]+)', $h);
            $keylist[] = $key;
        }

        $h = str_replace(['/'], ['\/'], $h);
        if (false !== strpos($h, '*')) {
            $keylist[] = '*';
            $h = str_replace('*', '(.*)', $h);
        }

        return $h;
    }

    public static function match_handler($loc, $method, &$args = null, &$path = null) 
    {
        if (!is_array($args)) $args = [];

        // First, all without wildcards
        foreach(self::path_handler_list() as $path) {
            if (false === strpos($path, '*')) {
                $keys = [];
                $pattern = self::handler_pattern($path, $keys);
                $matches = [];

                if (preg_match('/^' . $pattern . '$/', $loc, $matches)) {
                    $args = [];
                    for($i = 0; $i < count($keys); $i++) {
                        $args[$keys[$i]] = $i + 1 < count($matches) ? $matches[$i + 1] : null;
                    }
                    return self::get($path, $method);
                }
            }
        }

        // Second, all with wildcards
        foreach(self::path_handler_list() as $path) {
            if (false !== strpos($path, '*')) {
                $keys = [];
                $pattern = self::handler_pattern($path, $keys);
                $matches = [];

                if (preg_match('/^' . $pattern . '$/', $loc, $matches)) {
                    $args = [];
                    for($i = 0; $i < count($keys); $i++) {
                        $args[$keys[$i]] = $i + 1 < count($matches) ? $matches[$i + 1] : null;
                    }
                    return self::get($path, $method);
                }
            }
        }

        $args = null;
        $pattern = null;
        return null;
    }
}
