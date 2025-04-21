<?php

class Page
{
    private $template;

    public function __construct($template)
    {
        if (!file_exists($template)) {
            throw new Exception("Template file not found: $template");
        }
        $this->template = $template;
    }

    public function Render($data)
    {
        ob_start();
        extract($data);
        include $this->template;
        return ob_get_clean();
    }
}
