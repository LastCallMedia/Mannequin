<?php

namespace LastCall\Mannequin\Core\Console\Command;

use LastCall\Mannequin\Core\Engine\EngineInterface;
use LastCall\Mannequin\Core\Pattern\PatternCollection;
use LastCall\Mannequin\Core\Ui\FileWriter;
use LastCall\Mannequin\Core\Ui\HtmlDecorator;
use LastCall\Mannequin\Core\Ui\Manifester;
use LastCall\Mannequin\Core\Ui\UiInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class RenderCommand extends Command {

  private $manifester;
  private $engine;
  private $collection;
  private $assetMappings;
  private $decorator;

  public function __construct($name = NULL, Manifester $manifester, EngineInterface $engine, PatternCollection $collection, HtmlDecorator $decorator, UiInterface $ui, array $assetMappings = []) {
    parent::__construct($name);
    $this->manifester = $manifester;
    $this->engine = $engine;
    $this->collection = $collection;
    $this->assetMappings = $assetMappings;
    $this->decorator = $decorator;
    $this->ui = $ui;
  }

  public function configure() {
    $this->addOption('output-dir', 'o', InputOption::VALUE_OPTIONAL, 'The directory to output the UI in', 'mannequin');
  }

  public function execute(InputInterface $input, OutputInterface $output) {
    $io = new SymfonyStyle($input, $output);

    $outDir = $input->getOption('output-dir');

    $writer = new FileWriter($outDir);
    try {
      $manifest = $this->manifester->generate($this->collection);
      $writer->raw('manifest.json', json_encode($manifest));
      $rows[] = $this->getSuccessRow('Manifest');

      foreach($manifest['patterns'] as $patternManifest) {
        try {
          $pattern = $this->collection->get($patternManifest['id']);
          $writer->raw($patternManifest['source'], $this->engine->renderSource($pattern));

          foreach($patternManifest['sets'] as $setManifest) {
            $set = $pattern->getVariableSets()[$setManifest['id']];
            $rendered = $this->engine->render($pattern, $set);
            $writer->raw($setManifest['source'], $rendered->getMarkup());
            $writer->raw($setManifest['rendered'], $this->decorator->decorate($rendered->getMarkup(), $rendered->getScripts(), $rendered->getStyles()));
          }
          $rows[] = $this->getSuccessRow($pattern->getName());
        }
        catch(\Exception $e) {
          $rows[] = $this->getErrorRow($pattern->getName(), $e);
        }

      }
    }
    catch(\Exception $e) {
      $rows[] = $this->getErrorRow('Manifest');
    }

    foreach($this->assetMappings as $src => $dest) {
      $writer->copy($src, $dest);
    }

    foreach($this->ui->files() as $dest => $src) {
      $writer->copy($src, $dest);
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
