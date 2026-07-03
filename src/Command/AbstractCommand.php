<?php

declare(strict_types=1);

namespace App\Command;

use RuntimeException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

abstract class AbstractCommand extends Command
{
    protected SymfonyStyle $io;
    protected InputInterface $input;
    protected OutputInterface $output;

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        set_time_limit(0);

        $this->input = $input;
        $this->output = $output;
        $this->io = new SymfonyStyle($input, $output);

        return Command::SUCCESS;
    }

    protected function failWithError(string $message, int $returnCode = Command::FAILURE): int
    {
        $this->io->error($message);

        return $returnCode;
    }
    
    protected function getEnvVar(string $paramName): string
    {
        if (!isset($_SERVER[$paramName])) {
            throw new RuntimeException("Env variable '$paramName' not found");
        }
        
        return $_SERVER[$paramName];
    }
    
    protected function isWindows(): bool
    {
        return strtoupper(substr(PHP_OS, 0, 3)) === 'WIN';
    }
}
