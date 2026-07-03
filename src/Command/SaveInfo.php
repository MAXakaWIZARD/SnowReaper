<?php
declare(strict_types = 1);

namespace App\Command;

use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Helper\TableSeparator;
use Symfony\Component\Console\Helper\TableStyle;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SaveInfo extends SaveManipulationCommand
{
    protected function configure(): void
    {
        parent::configure();
        $this->setDescription('Show save file info');
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        parent::execute($input, $output);

        $saveFile = $this->loadSaveFile($input);
        if (!$saveFile) {
            return self::FAILURE;
        }
        
        $table = new Table($output);

        $sizeStyle = new TableStyle();
        $sizeStyle->setPadType(STR_PAD_RIGHT);
        $table->setColumnStyle(1, $sizeStyle);
        
        $table->addRow(['Slot', $saveFile->slot]);
        $table->addRow(['Dir', $_SERVER['SAVES_DIR']]);
        $table->addRow(['File', $saveFile->fileName]);
        
        $table->addRow(new TableSeparator());
        
        $table->addRow(['Hard mode', (int) $saveFile->isHardMode()]);
        $table->addRow(['Difficulty', $saveFile->getDifficultyName()]);
        $table->addRow(['Money', number_format(num: $saveFile->getMoney(), thousands_separator: ' ')]);
        
        $table->addRow(new TableSeparator());
        
        $diffSettings = $saveFile->getDifficultySettings();
        $table->addRow(['Deploy price', json_encode($diffSettings->deployPrice)]);
        
        $table->addRow(new TableSeparator());
        
        $table->addRow(['Recovery price', json_encode($diffSettings->recoveryPrice)]);
        $table->addRow(['Recovery price factor', $diffSettings->recoveryPriceFactor]);
        $table->addRow(['Region travelling factor', $diffSettings->regionTravellingPriceFactor]);
        $table->addRow(['Teleportation price', $diffSettings->teleportationPrice]);
        
        $table->addRow(new TableSeparator());
        
        $table->addRow(['Autoload price', $diffSettings->autoloadPrice]);
        $table->addRow(['Autoload price factor', $diffSettings->loadingPriceFactor]);
        
        $table->addRow(new TableSeparator());
        
        $table->addRow(['Tasks payouts factor', $diffSettings->tasksAndContestsPayoutsFactor]);
        $table->addRow(['Truck pricing factor', $diffSettings->truckPricingFactor]);
        $table->addRow(['Truck selling factor', $diffSettings->truckSellingFactor]);
        $table->addRow(['Addons pricing factor', $diffSettings->addonPricingFactor]);
        $table->addRow(['Addons selling factor', $diffSettings->sellMultiplier]);
        $table->addRow(['Trailer selling factor', $diffSettings->trailerSellingFactor]);
        
        $table->addRow(new TableSeparator());
        
        $table->addRow(['Garage fuel available', $diffSettings->isGarageRefuelAvailable ? 'yes' : 'no']);
        $table->addRow(['Fuel price factor', $diffSettings->fuelPriceFactor]);
        
        $table->addRow(new TableSeparator());
        
        $table->addRow(['Garage auto repair', $diffSettings->garageRepairePriceFactor ? 'no' : 'yes']);
        $table->addRow(['Garage repair price', json_encode($diffSettings->fullRepairPrice)]);
        $table->addRow(['Garage repair price factor', $diffSettings->garageRepairePriceFactor]);
        $table->addRow(['Repair points required factor', $diffSettings->repairPointsRequiredFactor]);
        $table->addRow(['Repair points cost factor', $diffSettings->repairPointsCostFactor]);
        $table->addRow(['Region repair money factor', $diffSettings->regionRepaireMoneyFactor]);
        $table->addRow(['Vehicle damage factor', $diffSettings->vehicleDamageFactor]);
        
        $table->render();
        
        return self::SUCCESS;
    }
}
