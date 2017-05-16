<?php


namespace LastCall\Mannequin\Drupal\Twig;


class DrupalTwigExtension extends \Twig_Extension {

  public function getFilters() {
    return [
      new \Twig_SimpleFilter('t', [$this, 'filterT']),
      new \Twig_SimpleFilter('without', [$this, 'filterWithout']),
      new \Twig_SimpleFilter('clean_id', '\Drupal\Component\Utility\Html::getId'),
      new \Twig_SimpleFilter('clean_class', '\Drupal\Component\Utility\Html::getClass'),
      new \Twig_SimpleFilter('safe_join', [$this, 'filterSafeJoin'], ['needs_environment' => TRUE, 'is_safe' => ['html']])
    ];
  }

  public function getFunctions() {
    return [
      new \Twig_SimpleFunction('path', [$this, 'functionPath']),
      new \Twig_SimpleFunction('link', [$this, 'functionLink']),
    ];
  }

  public static function filterT($str) {
    return $str;
  }

  public static function filterWithout($element) {
    if ($element instanceof \ArrayAccess) {
      $filtered_element = clone $element;
    }
    else {
      $filtered_element = $element;
    }
    $args = func_get_args();
    unset($args[0]);
    foreach ($args as $arg) {
      if (isset($filtered_element[$arg])) {
        unset($filtered_element[$arg]);
      }
    }
    return $filtered_element;
  }

  public static function functionPath() {
    return '/';
  }

  public static function functionLink($text, $url, $attributes = []) {
    return 'Link';
  }

  public function filterSafeJoin(\Twig_Environment $env, $value, $glue = '') {
    if ($value instanceof \Traversable) {
      $value = iterator_to_array($value, FALSE);
    }

    return implode($glue, array_map(function($item) use ($env) {
      // If $item is not marked safe then it will be escaped.
      return $this->escapeFilter($env, $item, 'html', NULL, TRUE);
    }, (array) $value));
  }
}