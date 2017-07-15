<?php

namespace LastCall\Mannequin\Core\Discovery;

trait IdEncoder
{
    protected function encodeId($id)
    {
        return md5($id);
    }
}
