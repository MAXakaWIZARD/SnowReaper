<?php
declare(strict_types = 1);

namespace App\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class SaveAddMoney extends SaveManipulationCommand
{
    protected function configure(): void
    {
        parent::configure();
        
        $this->setDescription('Add money');
        $this->addOption(
            'amount',
            'a',
            InputOption::VALUE_REQUIRED,
            'amount',
            100000
        );
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        parent::execute($input, $output);

        $saveFile = $this->loadSaveFile($input);
        if (!$saveFile) {
            return self::FAILURE;
        }
        
        $amount = (int) $input->getOption('amount');
        
        $this->io->title(sprintf(
            'Adding %s to save slot %d',
            number_format(num: $amount, thousands_separator: ' '),
            $saveFile->slot,
        ));
        $this->io->writeln('Before: ' . number_format(num: $saveFile->getMoney(), thousands_separator: ' '));
        
        try {
            $saveFile->addMoney($amount);
            $this->io->writeln('After:  ' . number_format(num: $saveFile->getMoney(), thousands_separator: ' '));
            $saveFile->write();
        } catch (\Exception $e) {
            $this->io->error('Failed to write save file: ' . $e->getMessage());
            return self::FAILURE;
        }
        
        $this->io->success('Done');
        
        return self::SUCCESS;
    }
}
