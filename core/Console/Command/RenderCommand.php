<?php

namespace LastCall\Mannequin\Core\Console\Command;

use LastCall\Mannequin\Core\Pattern\PatternCollection;
use LastCall\Mannequin\Core\Ui\UiWriter;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Input\InputOption;

class RenderCommand extends Command {

  private $uiWriter;
  private $collection;
  private $assetMappings;

  public function __construct($name = NULL, UiWriter $uiWriter, PatternCollection $collection, array $assetMappings = []) {
    parent::__construct($name);
    $this->uiWriter = $uiWriter;
    $this->collection = $collection;
    $this->assetMappings = $assetMappings;
  }

  public function configure() {
    $this->addOption('output-dir', 'o', InputOption::VALUE_OPTIONAL, 'The directory to output the UI in', 'mannequin');
  }

  public function execute(InputInterface $input, OutputInterface $output) {
    $io = new SymfonyStyle($input, $output);

    $outDir = $input->getOption('output-dir');

    $this->uiWriter->prepare($outDir);
    foreach($this->collection as $pattern) {
      try {
        $this->uiWriter->writeRender($pattern, realpath($outDir));
        $this->uiWriter->writeSource($pattern, realpath($outDir));
        $rows[] = $this->getSuccessRow(sprintf('Pattern: %s', $pattern->getName()));
      }
      catch(\Exception $e) {
        $rows[] = $this->getErrorRow(sprintf('Pattern: %s', $pattern->getName()), $e);
      }
    }

    try {
      $this->uiWriter->writeManifest($this->collection, $outDir);
      $rows[] = $this->getSuccessRow('Manifest');
    }
    catch(\Exception $e) {
      $rows[] = $this->getErrorRow('Manifest', $e);
    }

    try {
      $this->uiWriter->writeAssets($this->assetMappings, $outDir);
      $rows[] = $this->getSuccessRow('Assets');
    }
    catch(\Exception $e) {
      $rows[] = $this->getErrorRow('Assets', $e);
    }

    try {
      $this->uiWriter->writeUi($outDir);
      $rows[] = $this->getSuccessRow('UI');
    }
    catch(\Exception $e) {
      $rows[] = $this->getErrorRow('UI', $e);
    }

    $io->table(['', 'Name', 'Message'], $rows);
  }

  private function getSuccessRow($name) {
    return ['<info>✓</info>', $name, ''];
  }

  private function getErrorRow($name, \Exception $e) {
    return ['<error>x</error>', $name, $e->getMessage()];
  }
}