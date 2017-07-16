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

use LastCall\Mannequin\Core\Console\Command\ServerCommand;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\ProcessBuilder;

class ServerCommandTest extends TestCase
{
    public function testExecutesCommand()
    {
        $command = new ServerCommand('server', __FILE__, __FILE__);
        $builder = $this->prophesize(ProcessBuilder::class);
        $builder
            ->setArguments([
                'php',
                '-S',
                '127.0.0.1:8000',
                realpath(__DIR__.'/../../../Resources/router.php'),
            ])
            ->shouldBeCalled()
            ->willReturn($builder);

        $builder
            ->addEnvironmentVariables([
                'MANNEQUIN_CONFIG' => __FILE__,
                'MANNEQUIN_AUTOLOAD' => __FILE__,
                'MANNEQUIN_DEBUG' => false,
            ])
            ->shouldBeCalled()
            ->willReturn($builder);
        $builder
            ->setWorkingDirectory(realpath(__DIR__.'/../../../Resources/'))
            ->shouldBeCalled()->willReturn($builder);
        $builder
            ->setTimeout(null)
            ->shouldBeCalled()
            ->willReturn($builder);

        $process = new Process('test 0');
        $builder
            ->getProcess()
            ->willReturn($process);

        $command->setProcessBuilder($builder->reveal());

        $tester = new CommandTester($command);
        $tester->execute([
            'server',
        ]);
    }
}
