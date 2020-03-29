<?php

class page_render {
    private $pagedef;

    public function __construct()
    {
        $n = func_num_args();
        $a = func_get_args();
        if ($n >= 1) $this->pagedef = $a[0];
    }

    public function make_page_xsl() {
        $filename = __DIR__ . "/stylesheets/make-page.xsl";
        return file_get_contents($filename);
    }

    public function pagedef_get($path) { return $this->pagedef->get($path); }

    public function make_page($def) {
        $template_name = $this->pagedef_get("/pagedef/@template");
        $template_file = resource_resolver::instance()->resolve_file("template.xml", "template", $template_name);
        if ($template_file == null) return null;
        $template_xsl = file_get_contents($template_file);
        $result = xml_file::transformXMLXSL_static(file_get_contents($template), $this->make_page_xsl());

        $result = $def->transformXsl($template);

        $result = xml_file::transformXMLXSL_static($defXML)
    }
}
