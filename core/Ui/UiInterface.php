<?php


namespace LastCall\Mannequin\Core\Ui;


use LastCall\Mannequin\Core\Rendered;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

interface UiInterface {

  public function files(): array;
  public function isUiFile(string $path): bool;
  public function getUiFileResponse(string $path, Request $request): Response;
  public function decorateRendered(Rendered $rendered): string;
}