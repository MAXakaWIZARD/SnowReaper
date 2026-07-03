<?php
declare(strict_types = 1);

namespace App\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Helper\TableSeparator;
use Symfony\Component\Console\Helper\TableStyle;

class ModsList extends ModsManipulationCommand
{
    protected function configure(): void
    {
        parent::configure();
        $this->setDescription('List mods');
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        parent::execute($input, $output);
        
        $table = new Table($output);

        $idStyle = new TableStyle();
        $idStyle->setPadType(STR_PAD_LEFT);
        $table->setColumnStyle(0, $idStyle);
        
        $table->setHeaders(['ID', 'Dir', 'Pak', 'Title']);
        
        foreach ($this->getMods() as $mod) {
            $table->addRow([
                $mod->id, 
                $mod->dirName, 
                $mod->pakName, 
                $mod->title,
            ]);
        }
        
        $table->render();
        
        return self::SUCCESS;
    }
}
