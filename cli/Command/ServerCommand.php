<?php


namespace LastCall\Mannequin\Cli\Command;

use LastCall\Mannequin\Cli\Writer\UiWriter;
use Symfony\Bundle\WebServerBundle\WebServer;
use Symfony\Bundle\WebServerBundle\WebServerConfig;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\InvalidOptionException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Filesystem\Filesystem;

class ServerCommand extends Command {

  public function __construct($name = NULL, UiWriter $uiWriter) {
    parent::__construct($name);
    $this->uiWriter = $uiWriter;
  }

  public function configure() {
    $this->addArgument('address', InputArgument::OPTIONAL, 'The address to run on.');
    $this->addOption('output-dir', 'o', InputOption::VALUE_OPTIONAL, 'The directory to output the UI in');
  }

  public function execute(InputInterface $input, OutputInterface $output) {
    $io = new SymfonyStyle($input, $output);

    $config = $this->getHelper('mannequin_config')->getConfig(getcwd().'/.patterns.php');
    $address = $input->getArgument('address');
    if($output = $input->getOption('output-dir')) {
      if(!is_dir($output) || !is_writable($output)) {
        throw new InvalidOptionException('output-dir does not exist or is not writeable');
      }
    }
    else {
      $output = sys_get_temp_dir().'/mannequin/ui';
      (new Filesystem())->mkdir($output);
    }

    try {
      // @todo: It would be awesome if we could watch the patterns for changes...
      $io->write('Writing UI');
      $this->uiWriter->writeAll($config, $output);

      $io->write('Starting server');
      $serverConfig = new WebServerConfig($output, $address);
      $server = new WebServer();
      $server->run($serverConfig, FALSE);
    }
    catch(\Exception $e) {
      $io->error($e->getMessage());
    }
  }
}