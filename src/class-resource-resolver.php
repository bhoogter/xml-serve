<?php

class resource_resolver
{
    // private static $_instance;
    // public static function instance($resource_root = "")
    // {
    //     $value = self::$_instance ? self::$_instance : (self::$_instance = new resource_resolver());
    //     if ($resource_root != "") $value->resource_root = $resource_root;
    //     return $value;
    // }

    protected static $resource_root;
    protected static $locations;

    public static function locations($resource_root = "")
    {
        if (!is_array(self::$locations)) self::init($resource_root);
        if ($resource_root != '') self::$resource_root = $resource_root;
        return self::$locations;
    }

    public function init($resource_root = "")
    {
        self::$locations = [];
        
        if ($resource_root == "") self::$resource_root = __DIR__ . "/content";
        else self::$resource_root = $resource_root;

        self::add_location("content");
        self::add_location("html");
        self::add_location("system");
        self::add_location("template", "templates/@@");
        self::add_location("module", "modules/@@");
    }

    private static function add_location($name, $loc = "")
    {
        if ($loc == "") $loc = $name;
        self::locations()[$name] = $loc;
    }

    private static function remove_location($name)
    {
        unset(self::locations()[$name]);
    }

    public static function resolve_files($resource, $types = [], $mappings = [], $subfolders = ['.', '*'])
    {
        // print "\n<br/>resource_resolver::resolve_files($resource, ..., ..., ...)"; print_r($types);
        if (is_string($types) && is_string($mappings)) {
            $mappings = [$types => $mappings];
            $types = [$types];
        }
        if (is_string($types)) $types = [$types];
        if (is_string($subfolders)) $subfolders = [$subfolders];

        $types += ['.', 'html'];

        // print "<br/>===   Types="; print_r($types);
        $res = [];
        foreach ($types as $type) {
            // print "\n<br/>resource_resolver::resolve_files - type=$type, res="; print_r($res);
            $type_loc = !!isset(self::locations()[$type]) ? self::locations()[$type] : $type;
            $type_loc = str_replace($type_loc, "@@", @$mappings[$type] || '');
            // TODO: Other Mappings...
            $loc = self::$resource_root . "/" . $type_loc;
            // print "\n<br/>resource_resolver::resolve - loc: $loc";
            // print_r(glob($loc."//./*"));
            foreach ($subfolders as $subfolder) {
                $subloc = "$loc/$subfolder";
                $pattern = "$subloc/$resource";
                // print "\n<br/>resource_resolver::resolve - matching pattern: $pattern";
                $res += glob($pattern);
            }
        }
        $num = count($res);
        // print "\n<br/>resource_resolver::resolve - matched $num items...";
        return $res;
    }

    public static function resolve_file($resource, $types = [], $mappings = [], $subfolders = ['.', '*'])
    {
        $res = self::resolve_files($resource, $types, $mappings, $subfolders);
        return count($res) > 0 ? $res[0] : null;
    }

    public static function resolve_ref($resource, $types = [], $mappings = [], $subfolders = ['.', '*'])
    {
        $filename = self::resolve_file($resource, $types, $mappings, $subfolders);
        $result = str_replace($filename, dirname(__DIR__), "");
        return $result;
    }
}
