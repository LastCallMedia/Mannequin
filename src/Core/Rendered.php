<?php

/*
 * This file is part of Mannequin.
 *
 * (c) 2017 Last Call Media, Rob Bayliss <rob@lastcallmedia.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LastCall\Mannequin\Core;

final class Rendered
{
    private $markup = '';

    private $css = [];

    private $js = [];

    public function __construct(array $styles = [], array $scripts = [])
    {
        $this->setCss($styles);
        $this->setJs($scripts);
    }

    public function getMarkup(): string
    {
        return $this->markup;
    }

    public function setMarkup(string $markup)
    {
        $this->markup = $markup;
    }

    public function addCss(array $css)
    {
        $this->css = array_merge($this->css, $css);
    }

    public function getCss(): array
    {
        return $this->css;
    }

    public function setCss(array $css)
    {
        $this->css = $css;
    }

    public function addJs(array $js)
    {
        $this->js = array_merge($this->js, $js);
    }

    public function getJs(): array
    {
        return $this->js;
    }

    public function setJs(array $js)
    {
        $this->js = $js;
    }

    public function __toString()
    {
        return $this->markup;
    }
}
