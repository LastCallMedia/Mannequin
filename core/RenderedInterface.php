<?php

namespace LastCall\Patterns\Core;

interface RenderedInterface {

  public function getId(): string;
  public function getName(): string;
  public function getMarkup(): string;
}