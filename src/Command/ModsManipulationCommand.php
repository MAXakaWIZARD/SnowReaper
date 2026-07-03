<?php
declare(strict_types = 1);

namespace App\Command;

use App\Mod;
use Symfony\Component\Finder\Finder;

abstract class ModsManipulationCommand extends AbstractCommand
{
    /** 
      @return Mod[] 
     */
    protected function getMods(): array
    {
        $mods = [];
        $modsSourceDir = $this->getEnvVar('MODS_SOURCE_DIR');
        
        $dirs = new Finder()
            ->directories()
            ->in($modsSourceDir)
            ->depth(0);
        foreach ($dirs as $dir) {
            $mods[] = new Mod($dir);
        }
        
        return $mods;
    }
}
