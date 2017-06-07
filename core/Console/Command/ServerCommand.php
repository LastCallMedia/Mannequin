<?php


namespace LastCall\Mannequin\Core\Console\Command;

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
use LastCall\Mannequin\Core\Application;

$autoload_file = '%s';
require $autoload_file;
$app = new Application([
  'debug' => %d,
  'autoload_file' => $autoload_file,
  'config_file' => '%s',
]);
$app->run();
eod;

  private $autoloadPath;
  private $configFile;
  private $debug;

  public function __construct($name = NULL, string $configFile, string $autoloadPath, bool $debug) {
    parent::__construct($name);
    $this->autoloadPath = $autoloadPath;
    $this->configFile = $configFile;
    $this->debug = $debug;
  }

  public function configure() {
    $this->addArgument('address', InputArgument::OPTIONAL, 'The address to run on.');
    $this->addOption('output-dir', 'o', InputOption::VALUE_OPTIONAL, 'The directory to output the UI in');
  }

  public function execute(InputInterface $input, OutputInterface $output) {
    $address = $input->getArgument('address');

    $dir = sys_get_temp_dir() .'/mannequin-server';
    $code = sprintf(self::CODE, $this->autoloadPath, $this->debug, realpath($this->configFile));
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