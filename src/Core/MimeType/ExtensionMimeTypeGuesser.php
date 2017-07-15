<?php

namespace LastCall\Mannequin\Core\MimeType;

use Symfony\Component\HttpFoundation\File\MimeType\MimeTypeGuesserInterface;

class ExtensionMimeTypeGuesser implements MimeTypeGuesserInterface
{
    public function guess($path)
    {
        $ext = pathinfo($path, PATHINFO_EXTENSION);
        switch ($ext) {
            case 'css':
                return 'text/css';
            case 'js':
                return 'application/javascript';
            default:
                return;
        }
    }
}
