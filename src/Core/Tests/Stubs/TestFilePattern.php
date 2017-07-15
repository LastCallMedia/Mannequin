<?php


namespace LastCall\Mannequin\Core\Tests\Stubs;


use LastCall\Mannequin\Core\Pattern\AbstractPattern;
use LastCall\Mannequin\Core\Pattern\TemplateFilePatternInterface;

class TestFilePattern extends AbstractPattern implements TemplateFilePatternInterface
{

    private $file;

    public function __construct($id, array $aliases = [], \SplFileInfo $file)
    {
        parent::__construct($id, $aliases);
        $this->file = $file;
    }

    public function getFile(): \SplFileInfo
    {
        return $this->file;
    }

    public function getRawFormat(): string
    {
        return 'html';
    }
}