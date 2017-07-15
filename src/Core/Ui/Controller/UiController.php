<?php


namespace LastCall\Mannequin\Core\Ui\Controller;

use LastCall\Mannequin\Core\Ui\UiInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class UiController {

  private $ui;

  public function __construct(UiInterface $ui) {
    $this->ui = $ui;
  }

  public function staticAction($name, Request $request) : Response {
    if($this->ui->isUiFile($name)) {
      return $this->ui->getUiFileResponse($name, $request);
    }
    // @todo: Assets need to be checked here.
    throw new NotFoundHttpException('Asset not found.');
  }
}