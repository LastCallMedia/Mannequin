<?php

namespace LastCall\Patterns\Core\Render;

interface RenderedInterface {

  public function getId(): string;
  public function getName(): string;
  public function getMarkup(): string;
}