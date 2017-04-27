<?php

namespace LastCall\Patterns\Cli\Command;

use LastCall\Patterns\Cli\Writer\FileWriter;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class RenderCommand extends Command {

  public function execute(InputInterface $input, OutputInterface $output) {
    $io = new SymfonyStyle($input, $output);
    /** @var \LastCall\Patterns\Core\Config $config */
    $config = $this->getHelper('patterns_config')->getConfig(getcwd().'/.patterns.php');
    $collection = $config->getCollection();
    $renderer = $config->getRenderer();
    $writer = new FileWriter(__DIR__.'/patterns');

    $io->block('Generating patterns for ' . $collection->getName());
    foreach($collection->getPatterns() as $pattern) {
      $io->block(sprintf('Generating %s', $pattern->getName()));
      $rendered = $renderer->render($pattern);
      $writer->write($rendered);
    }

    $writer->finalize();
  }
}