<?php

namespace LastCall\Mannequin\Core\Command;

use LastCall\Mannequin\Core\Ui\UiWriter;
use LastCall\Mannequin\Core\ConfigInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Console\Input\InputOption;

class RenderCommand extends Command {

  private $uiWriter;
  private $config;

  public function __construct($name = NULL, UiWriter $uiWriter, ConfigInterface $config) {
    parent::__construct($name);
    $this->uiWriter = $uiWriter;
    $this->config = $config;
  }

  public function configure() {
    $this->addOption('config', 'c', InputOption::VALUE_OPTIONAL, 'The path to a mannequin configuration file.');
    $this->addOption('output-dir', 'o', InputOption::VALUE_OPTIONAL, 'The directory to output the UI in', 'mannequin');
  }

  public function execute(InputInterface $input, OutputInterface $output) {
    $io = new SymfonyStyle($input, $output);

    $output = $input->getOption('output-dir');
    (new Filesystem())->mkdir($output);
    if(!is_dir($output) || !is_writable($output)) {
      throw new InvalidOptionException('output-dir does not exist or is not writeable');
    }

    $this->uiWriter->prepare($output);
    foreach($this->config->getCollection() as $pattern) {
      try {
        $this->uiWriter->writeRender($pattern, realpath($output));
      }
      catch(\Exception $e) {
        $io->warning($e->getMessage());
      }
    }
    $this->uiWriter->writeManifest($this->config->getCollection(), $output);
    $this->uiWriter->writeAssets($this->config->getAssetMappings(), $output);
    $this->uiWriter->writeUi($output);
  }
}