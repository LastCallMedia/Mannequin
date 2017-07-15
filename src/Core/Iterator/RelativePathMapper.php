<?php

namespace LastCall\Mannequin\Core\Iterator;

use Symfony\Component\Finder\SplFileInfo;

class RelativePathMapper
{
    public function __invoke($filename)
    {
        if ($filename instanceof SplFileInfo) {
            return [
                $filename->getPathname(),
                $filename->getRelativePathname(),
            ];
        }
        throw new \InvalidArgumentException(
            'RelativePathMapper can only accept Finder SplFileInfo instances.'
        );
    }
}
