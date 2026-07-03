<?php
declare(strict_types = 1);

namespace App\Command;

use App\SaveFile;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;

abstract class SaveManipulationCommand extends AbstractCommand
{
    protected function configure(): void
    {
        $this->addOption(
            name: 'slot',
            shortcut: 's',
            mode: InputOption::VALUE_REQUIRED,
            description: 'save slot',
            default: 1,
        );
    }
    
    protected function loadSaveFile(InputInterface $input): ?SaveFile
    {
        $slot = (int) $input->getOption('slot');
        
        try {
            return new SaveFile($_SERVER['SAVES_DIR'], $slot);
        } catch (\Exception $e) {
            $this->io->error('Failed to parse save file: ' . $e->getMessage());
        }
        
        return null;
    }
}
