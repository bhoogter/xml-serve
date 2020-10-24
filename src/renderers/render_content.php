<?php

class render_content extends render_base
{
    public static function init()
    {
        xml_serve::add_handler("content", get_class() . "::render");
    }

    public static function render($el)
    {
        php_logger::call(xml_file::toXml($el));
        $id = $el->getAttribute("id");
        if (!$id) $id = 'content';

        if (xml_serve::$pagedef) {
            $src = xml_serve::$pagedef->get("/pagedef/content[@id='$id']/@src");
            $type = xml_serve::$pagedef->get("/pagedef/content[@id='$id']/@type");
            $name = xml_serve::$pagedef->get("/pagedef/content[@id='$id']/@name");
        }

        if ($src == '' && xml_serve::$template) {
            $src = xml_serve::$template->get("/pagetemplate/content[@id='$id']/@src");
            $type = xml_serve::$template->get("/pagetemplate/content[@id='$id']/@type");
            $name = xml_serve::$template->get("/pagetemplate/content[@id='$id']/@name");
        }

        if ($src == '') {
            $src = xml_serve::$settings->get("/site/content[@id='$id']/@src");
            $type = xml_serve::$settings->get("/site/content[@id='$id']/@type");
            $name = xml_serve::$settings->get("/site/content[@id='$id']/@name");
        }

        if ($src == '') {
            $src = $el->getAttribute("src");
            $type = $el->getAttribute("type");
            $name = $el->getAttribute("name");
        }

        if ($src == "") $src = "$id.html";
        if ($type == "") $type = strrpos($src, '.') === false ? '' : substr($src, strrpos($src, '.') + 1);
        php_logger::debug("id=$id", "src=$src", "type=$type", "name=$name");

        if ($type != 'element') {
            $rTypes = ["template"];
            $rMapps = ["template"=>xml_serve::template_name()];
            if (xml_serve::$extension) {
                $rTypes[] = "module";
                $rMapps += ["module" => xml_serve::$extension];
            }
            $res = xml_serve::resource_resolver()->resolve_file($src, $rTypes, $rMapps);
            php_logger::log("template_name", xml_serve::template_name(), "res=$res");

            if ($res == "") return xml_serve::empty_content();
            $cont = file_get_contents($res);
        }

        switch (strtolower($type)) {
            case 'text':
            case 'txt':
                return xml_serve::xml_content($cont);
            case 'xml':
            case 'xhtml':
                php_logger::debug("parsing xhtml [len=" . strlen($cont) . "]: " . substr($cont, 10));
                return xml_serve::xml_content($cont);
            case 'html':
                $config = array(
                    'indent'         => true,
                    'output-xml'     => true,
                    'input-xml'     => true,
                    'wrap'         => '1000');
                $tidy = new tidy();
                $tidy->parseString($cont, $config, 'utf8');
                $tidy->cleanRepair();
                $cont = "<span>" . tidy_get_output($tidy) . "</span>";
                $result = xml_serve::xml_content($cont);
                return $result;
            case 'md':
                $html = xml_serve::markdownToHtml($cont, true);
                return xml_serve::xml_content(Slimdown::render($html));
            case 'element':
                return xml_file::toDocEl(xml_serve::handle_element($name, $el));
            default:
                return xml_serve::xml_content("<span>!<[CDATA[".str_replace(">", "&gt;", $cont)."]]></span>");
        }

        return $el;
    }
}
