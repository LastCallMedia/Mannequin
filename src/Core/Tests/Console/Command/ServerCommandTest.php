<?php

/*
 * This file is part of Mannequin.
 *
 * (c) 2017 Last Call Media, Rob Bayliss <rob@lastcallmedia.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LastCall\Mannequin\Core\Tests\Console\Command;

use LastCall\Mannequin\Core\Console\Command\StartCommand;
use LastCall\Mannequin\Core\MannequinConfig;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\ProcessBuilder;
use Symfony\Component\Console\Output\OutputInterface;

class ServerCommandTest extends TestCase
{
    public function getInputOutput()
    {
        return [
            [null, '0.0.0.0:8000'],
            ['127.0.0.1', '127.0.0.1:8000'],
            ['*:8002', '0.0.0.0:8002'],
            ['8002', '0.0.0.0:8002'],
        ];
    }

    /**
     * @dataProvider getInputOutput
     */
    public function testCommandIo($inputAddress, $expectedListenAddress)
    {
        $config = new MannequinConfig();
        $config->setDocroot(__DIR__);
        $command = new StartCommand('server', $config, __FILE__, __FILE__);
        $builder = $this->prophesize(ProcessBuilder::class);
        $builder
            ->setArguments([
                'php',
                '-S',
                $expectedListenAddress,
                realpath(__DIR__.'/../../../Resources/router.php'),
            ])
            ->shouldBeCalled()
            ->willReturn($builder);

        $builder
            ->addEnvironmentVariables([
                'MANNEQUIN_CONFIG' => __FILE__,
                'MANNEQUIN_AUTOLOAD' => __FILE__,
                'MANNEQUIN_DEBUG' => false,
                'MANNEQUIN_VERBOSITY' => OutputInterface::VERBOSITY_NORMAL,
            ])
            ->shouldBeCalled()
            ->willReturn($builder);
        $builder
            ->setWorkingDirectory(__DIR__)
            ->shouldBeCalled()->willReturn($builder);
        $builder
            ->setTimeout(null)
            ->shouldBeCalled()
            ->willReturn($builder);

        $process = new Process('/bin/true');
        $builder
            ->getProcess()
            ->willReturn($process);

        $command->setProcessBuilder($builder->reveal());

        $tester = new CommandTester($command);
        $tester->execute(['address' => $inputAddress]);

        $expectedOutput = sprintf('Visit http://%s in your web browser', $expectedListenAddress);
        $this->assertContains($expectedOutput, $tester->getDisplay());
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Port test is not valid
     */
    public function testInvalidPort()
    {
        $config = new MannequinConfig();
        $command = new StartCommand('server', $config, __FILE__, __FILE__);
        $builder = $this->prophesize(ProcessBuilder::class);
        $command->setProcessBuilder($builder->reveal());
        $tester = new CommandTester($command);
        $tester->execute(['address' => 'test:test']);
    }

    public function testOutputsConfigWarning()
    {
        $config = new MannequinConfig();
        $command = new StartCommand('server', $config, __FILE__, __FILE__);
        $builder = $this->prophesize(ProcessBuilder::class);
        $builder->setArguments(Argument::cetera())->willReturn($builder);
        $builder->addEnvironmentVariables(Argument::cetera())->willReturn($builder);
        $builder->setWorkingDirectory(Argument::cetera())->willReturn($builder);
        $builder->setTimeout(Argument::cetera())->willReturn($builder);
        $builder->getProcess()->willReturn(new Process('/bin/true'));

        $command->setProcessBuilder($builder->reveal());
        $tester = new CommandTester($command);
        $tester->execute(['address' => '*:8000']);
        $this->assertContains('This configuration does not have any extensions associated with it', $tester->getDisplay());
    }
}
