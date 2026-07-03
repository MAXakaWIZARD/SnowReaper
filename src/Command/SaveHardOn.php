<?php
declare(strict_types = 1);

namespace App\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SaveHardOn extends SaveManipulationCommand
{
    protected function configure(): void
    {
        parent::configure();
        $this->setDescription('Enable hard mode');
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        parent::execute($input, $output);

        $saveFile = $this->loadSaveFile($input);
        if (!$saveFile) {
            return self::FAILURE;
        }
        
        $this->io->title(sprintf(
            'Enabling hard mode on slot %d...',
            $saveFile->slot,
        ));
        
        try {
            $saveFile->setHardMode(true);
            $saveFile->write();
        } catch (\Exception $e) {
            $this->io->error('Failed to write save file: ' . $e->getMessage());
            return self::FAILURE;
        }
        
        $this->io->success('Done');
        
        return self::SUCCESS;
    }
}
