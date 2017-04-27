<?php


namespace LastCall\Patterns\Cli\Command;

use Symfony\Bundle\WebServerBundle\WebServer;
use Symfony\Bundle\WebServerBundle\WebServerConfig;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ServerCommand extends Command {

  public function configure() {
    $this->addArgument('address', InputArgument::OPTIONAL, 'The address to run on.');
  }

  public function execute(InputInterface $input, OutputInterface $output) {
    $io = new SymfonyStyle($input, $output);

    $address = $input->getArgument('address');
    $serverConfig = new WebServerConfig($this->getDocroot(), 'prod', $address);
    $server = new WebServer();
    try {
      $io->write('Starting server.');
      $server->run($serverConfig, FALSE);
    }
    catch (\Exception $e) {
      $io->error($e->getMessage());
    }

  }

  private function getDocroot() {
    return sprintf('%s%s..%s', __DIR__, DIRECTORY_SEPARATOR, DIRECTORY_SEPARATOR);
  }
}