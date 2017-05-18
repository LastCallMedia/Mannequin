<?php

namespace LastCall\Mannequin\Cli\Command;

use LastCall\Mannequin\Cli\Writer\FileWriter;
use LastCall\Mannequin\Cli\Writer\UiWriter;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Console\Input\InputOption;

class RenderCommand extends Command {

  private $uiWriter;

  public function __construct($name = NULL, UiWriter $uiWriter) {
    parent::__construct($name);
    $this->uiWriter = $uiWriter;
  }

  public function configure() {
    $this->addOption('config', 'c', InputOption::VALUE_OPTIONAL, 'The path to a mannequin configuration file.');
    $this->addOption('output-dir', 'o', InputOption::VALUE_OPTIONAL, 'The directory to output the UI in', 'mannequin');
  }

  public function execute(InputInterface $input, OutputInterface $output) {
    $io = new SymfonyStyle($input, $output);
    $configHelper = $this->getHelper('mannequin_config');

    $output = $input->getOption('output-dir');
    (new Filesystem())->mkdir($output);
    if(!is_dir($output) || !is_writable($output)) {
      throw new InvalidOptionException('output-dir does not exist or is not writeable');
    }

    /** @var \LastCall\Mannequin\Core\Config $config */
    $config = $configHelper->getConfig($input->getOption('config') ?: getcwd().'/.mannequin.php');
    $io->block(sprintf('Generating patterns into %s/', $output));
    $this->uiWriter->writeAll($config,$output);
  }
}