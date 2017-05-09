<?php

namespace LastCall\Patterns\Core\Extension;

use LastCall\Patterns\Core\ConfigInterface;

interface ExtensionInterface {

  public function setConfig(ConfigInterface $container);

  public function getParsers(): array;

  public function getDiscoverers(): array;

  public function getRenderers(): array;

  public function getVariableFactories(): array;

  public function getLabels(): array;
}