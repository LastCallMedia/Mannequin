<?php


namespace LastCall\Mannequin\Twig;


interface TwigInspectorInterface {

  public function inspectLinked(\Twig_Source $source): array;
}