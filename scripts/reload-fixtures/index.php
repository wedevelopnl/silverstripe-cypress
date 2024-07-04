<?php
declare(strict_types=1);

require __DIR__ . '/../../../../autoload.php';

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

if (!class_exists(Process::class)) {
    http_response_code(500);
    echo 'Install `symfony/console` inorder to enable this endpoint.';
    exit;
}

set_time_limit(600);
ini_set('memory_limit', '-1');

function findPhpBinary()
{
    if (defined('PHP_BINARY') && PHP_BINARY) {
        return PHP_BINARY;
    }

    $paths = [
        '/usr/bin/php',
        '/usr/local/bin/php',
        '/usr/sbin/php',
        '/usr/local/sbin/php',
    ];

    foreach ($paths as $path) {
        if (is_executable($path)) {
            return $path;
        }
    }

    throw new RuntimeException('Unable to locate PHP binary.');
}

$command = new class extends Command {
    protected static $defaultName = 'wedevelop:reload-fixtures';
    private const string SAKE_PATH = __DIR__ . '/../../../../silverstripe/framework/cli-script.php';
    private const int PROCESS_TIMEOUT = 600;

    protected function configure()
    {
        $this
            ->setDescription('Reloads the fixtures');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        if (!is_executable(self::SAKE_PATH)) {
            $output->writeln('Could not execute sake.');
            return Command::FAILURE;
        }

        try {
            $process = new Process([
                findPhpBinary(),
                self::SAKE_PATH,
                'dev/tasks/load-fixtures',
                'directory=tests/fixtures',
            ]);
        } catch(RuntimeException) {
            $output->writeln('Couldn\'t find php binairy');
        }
        $process->setWorkingDirectory('/app');
        $process->setTimeout(self::PROCESS_TIMEOUT);

        try {
            $process->mustRun();
            $output->write($process->getOutput());
        } catch (ProcessFailedException $exception) {
            $output->writeln('The task failed: ' . $exception->getMessage());
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
};

$application = new Application();
$application->add($command);

$command = $application->find('wedevelop:reload-fixtures');

$input = new ArrayInput([]);
$output = new BufferedOutput();

$resultCode = $command->run($input, $output);

if ($resultCode !== Command::SUCCESS) {
    http_response_code(500);
    echo $output->fetch();
}
else {
    http_response_code(204);
}
