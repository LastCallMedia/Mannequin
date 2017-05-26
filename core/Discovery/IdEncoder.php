<?php


namespace LastCall\Mannequin\Core\Discovery;


trait IdEncoder {

  protected function encodeId($id) {
    return base64_encode($id);
  }
}