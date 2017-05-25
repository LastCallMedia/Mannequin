<?php


namespace LastCall\Mannequin\Cli\Command;

use LastCall\Mannequin\Cli\Writer\UiWriter;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Process\ProcessBuilder;

class ServerCommand extends Command {

  const CODE = <<<'eod'
<?php
use LastCall\Mannequin\Cli\Application;

$autoload_file = '%s';
require $autoload_file;
$app = new Application([
  'autoload_file' => $autoload_file,
  'config_file' => '%s',
]);
$app->run();
eod;

  private $autoloadPath;
  private $configFile;


  public function __construct($name = NULL, $configFile, $autoloadPath) {
    parent::__construct($name);
    $this->autoloadPath = $autoloadPath;
    $this->configFile = $configFile;
  }

  public function configure() {
    $this->addArgument('address', InputArgument::OPTIONAL, 'The address to run on.');
    $this->addOption('config', 'c', InputOption::VALUE_OPTIONAL, 'The path to a mannequin configuration file.');
    $this->addOption('output-dir', 'o', InputOption::VALUE_OPTIONAL, 'The directory to output the UI in');
  }

  public function execute(InputInterface $input, OutputInterface $output) {
    $address = $input->getArgument('address');

    $dir = sys_get_temp_dir() .'/mannequin-server';
    $code = sprintf(self::CODE, $this->autoloadPath, realpath($this->configFile));
    (new Filesystem())->mkdir($dir);
    file_put_contents($dir.'/index.php', $code);
    return $this->runserver($dir, $address, $output);
  }

  private function runServer($docroot, $address, OutputInterface $output) {
    $routerFile = realpath(__DIR__.'/../Resources/router.php');
    $builder = new ProcessBuilder(['php', '-S', $address, $routerFile]);
    $builder->setWorkingDirectory($docroot);
    $builder->setTimeout(null);
    return $builder->getProcess()
      ->setTty(TRUE)
      ->run();
  }
}