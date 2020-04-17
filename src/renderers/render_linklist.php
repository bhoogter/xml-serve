<?php

class render_linklist extends render_base
{
    public static function init()
    {
        php_logger::log("CALL");
        page_render::add_handler("linklist", get_class()."::render");
    }

    public static function render($el) 
    {
        php_logger::log("CALL");
        return page_render::empty_content();
    }
}