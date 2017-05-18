<?php

namespace LastCall\Mannequin\Cli\Command;

use LastCall\Mannequin\Cli\Writer\FileWriter;
use LastCall\Mannequin\Cli\Writer\UiWriter;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class RenderCommand extends Command {

  private $uiWriter;

  public function __construct($name = NULL, UiWriter $uiWriter) {
    parent::__construct($name);
    $this->uiWriter = $uiWriter;
  }

  public function execute(InputInterface $input, OutputInterface $output) {
    $io = new SymfonyStyle($input, $output);
    /** @var \LastCall\Mannequin\Core\Config $config */
    $config = $this->getHelper('mannequin_config')->getConfig(getcwd().'/.patterns.php');
    $io->block('Generating patterns...');
    $this->uiWriter->writeAll($config,getcwd().'/output');
  }
}