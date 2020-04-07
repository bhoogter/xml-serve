<?php

public class make_page_render_content {
    public static function render_content($El) {
        $f = new xml_file($El);
        $type = $f->get("@id");
        print "..................type=$type";
        return $f->Doc;
    }
    
}