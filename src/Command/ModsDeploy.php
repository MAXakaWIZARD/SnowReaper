<?php
declare(strict_types=1);

namespace App\Command;

use App\Mod;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

class ModsDeploy extends ModsManipulationCommand
{
    protected function configure(): void
    {
        parent::configure();
        $this->setDescription('Deploy mods');

        $this->addArgument(
            name: 'filter',
            mode: InputArgument::OPTIONAL,
            description: 'filter',
            default: '',
        );
        $this->addOption(
            name: 'dry-run',
            mode: InputOption::VALUE_NONE,
            description: 'execute dry run - do not archive anything',
        );
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        parent::execute($input, $output);
        
        $dryRun = (bool) $input->getOption('dry-run');

        if (!$this->isWindows() && !$dryRun) {
            $this->io->error('Deploy mods is supposed to be run under Windows');
            return self::FAILURE;
        }

        try {
            $winrarBin = $this->getEnvVar('WINRAR_BIN');
            $modsDeployDir = $this->getEnvVar('MODS_DEPLOY_DIR');
        } catch (\Exception $e) {
            $this->io->error($e->getMessage());
            return self::FAILURE;
        }

        if (!is_file($winrarBin)) {
            $this->io->error("WinRAR binary not found: $winrarBin");
            return self::FAILURE;
        }

        if (!is_dir($modsDeployDir)) {
            $this->io->error("Mods deploy directory not found: $modsDeployDir");
            return self::FAILURE;
        }

        $totalCount = 0;
        $matchedCount = 0;
        $updatedCount = 0;
        $failedCount = 0;

        $filter = strtolower($input->getArgument('filter'));
        
        $this->io->title(sprintf(
            '%s%s%s',
            'Deploying mods',
            $filter ? " with filter: $filter" : '',
            $dryRun ? ' (DRY RUN)' : '',
        ));

        $mods = $this->getMods();
        foreach ($mods as $mod) {
            $totalCount++;
            
            if (!$this->isMatching($mod, $filter)) {
                continue;
            }

            $matchedCount++;

            $this->io->section($mod->title);

            $targetPakFilePath = $modsDeployDir . DS . $mod->id . DS . $mod->pakName . '.pak';
            if (!file_exists($targetPakFilePath)) {
                $failedCount++;
                $this->io->error("Target pak file not found: $targetPakFilePath");
                continue;
            }

            $cmd = [
                $winrarBin,
                'u',
                '-r',
                '-ep1',
                $targetPakFilePath,
                $mod->pakDirPath . DS. '*',
            ];

            $process = new Process(
                command: $cmd,
                timeout: 5 * 60
            );

            if ($this->io->isVerbose()) {
                $this->io->outlineBlock($process->getCommandLine(), 'About to execute');
            }

            if (!$dryRun) {
                $process->run();
            }

            if ($process->isSuccessful() || $dryRun) {
                $updatedCount++;
                $this->io->success("Done");
            } else {
                $failedCount++;
                $this->io->error("Failed: {$process->getErrorOutput()}");
            }
        }

        $this->io->title('Results');
        $this->io->writeln("Total: $totalCount");
        $this->io->writeln("Matched: $matchedCount");
        $this->io->writeln("Updated: $updatedCount");
        $this->io->writeln("Failed: $failedCount");

        return self::SUCCESS;
    }

    protected function isMatching(Mod $mod, string $filter): bool
    {
        if (!$filter) {
            return true;
        }

        if (str_contains(strtolower($mod->dirName), $filter)
            || str_contains(strtolower($mod->pakName), $filter)
        ) {
            return true;
        }

        return false;
    }
}
