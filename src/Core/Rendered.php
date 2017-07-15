<?php

namespace LastCall\Mannequin\Core;

final class Rendered
{

    private $markup = '';

    private $styles = [];

    private $scripts = [];

    public function __construct(array $styles = [], array $scripts = [])
    {
        $this->setStyles($styles);
        $this->setScripts($scripts);
    }

    public function getMarkup(): string
    {
        return $this->markup;
    }

    public function setMarkup(string $markup)
    {
        $this->markup = $markup;
    }

    public function addStyles(array $styles)
    {
        $this->styles = array_merge($this->styles, $styles);
    }

    public function getStyles(): array
    {
        return $this->styles;
    }

    public function setStyles(array $styles)
    {
        $this->styles = $styles;
    }

    public function addScripts(array $scripts)
    {
        $this->scripts = array_merge($this->scripts, $scripts);
    }

    public function getScripts(): array
    {
        return $this->scripts;
    }

    public function setScripts(array $scripts)
    {
        $this->scripts = $scripts;
    }

    public function __toString()
    {
        return $this->markup;
    }
}