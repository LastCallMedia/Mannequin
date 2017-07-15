<?php


namespace LastCall\Mannequin\Core\Ui;

use LastCall\Mannequin\Core\Rendered;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class MannequinUi implements UiInterface
{

    const TEMPLATE = <<<'EOD'
<html>
<head>
  %s
  %s
</head>
<body>
  %s
</body>
EOD;

    private $uiPath;

    public function __construct(string $uiPath)
    {
        $this->uiPath = $uiPath;
    }

    public function files(): array
    {
        $manifest = file_get_contents($this->uiPath('asset-manifest.json'));
        $files = [];
        foreach (json_decode($manifest, true) as $file) {
            $files[$file] = $this->uiPath($file);
        }
        $files['index.html'] = $this->uiPath('index.html');

        return $files;
    }

    private function uiPath($relativePath = '')
    {
        return rtrim(sprintf('%s/%s', $this->uiPath, $relativePath), '/');
    }

    public function isUiFile(string $path): bool
    {
        return file_exists($this->uiPath($path));
    }

    public function getUiFileResponse(string $path, Request $request): Response
    {
        return new BinaryFileResponse($this->uiPath($path));
    }

    public function decorateRendered(Rendered $rendered): string
    {
        return sprintf(
            self::TEMPLATE,
            $this->mapAssets(
                $rendered->getScripts(),
                '<script type="text/javascript" src="%s"></script>'
            ),
            $this->mapAssets(
                $rendered->getStyles(),
                '<link rel="stylesheet" href="%s" />'
            ),
            $rendered->getMarkup()
        );
    }

    private function mapAssets(array $assets, $pattern)
    {
        return implode(
            "\n",
            array_map(
                function ($asset) use ($pattern) {
                    return sprintf($pattern, $asset);
                },
                $assets
            )
        );
    }
}