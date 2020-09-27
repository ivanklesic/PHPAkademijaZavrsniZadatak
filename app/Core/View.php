<?php


namespace App\Core;

class View
{
    private $layout;

    public function __construct($layout = "layout")
    {
        $this->layout = basename($layout);
    }

    public function render($name, $args = [])
    {
        ob_start();
        extract($args);
        include BP . DIRECTORY_SEPARATOR . "app/view/$name.phtml";
        $content = ob_get_clean();

        if ($this->layout) {
            include BP . DIRECTORY_SEPARATOR . "app/view/{$this->layout}.phtml";
        } else {
            echo $content;
        }
        return $this;
    }

    public function formatDate(string $date, string $format = 'd.m. H:i'): string
    {
        $date = new \DateTime($date);
        return $date->format($format);
    }
}