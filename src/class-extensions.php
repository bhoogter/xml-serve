<?php

class xml_serve_extensions
{
    protected static $extension_folder;

    public static function init($fld)
    {
        if (substr($fld, -1) == '/') $fld = substr($fld, 0, strlen($fld) - 1);
        self::$extension_folder = $fld;
    }

    public static function extenion_list()
    {
        $src = [];
        $src += glob(self::$extension_folder . "/*/");
        $src += glob(self::$extension_folder . "/*.phar");
        return $src;
    }

    public static function extension_type($ext)
    {
        if (is_dir(self::$extension_folder . "/$ext")) return "SRC";
        if (file_exists(self::$extension_folder . "/$ext.phar")) return "PHAR";
        return null;
    }

    public static function get_file($ext, $f)
    {
        if (!self::extension_type($ext)) return null;
    }
}
