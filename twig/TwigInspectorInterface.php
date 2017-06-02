<?php


namespace LastCall\Mannequin\Twig;


interface TwigInspectorInterface {

  public function inspectLinked(\Twig_Source $source): array;

  /**
   * @param \Twig_Source $source
   *
   * @return string|FALSE
   */
  public function inspectPatternData(\Twig_Source $source);
}