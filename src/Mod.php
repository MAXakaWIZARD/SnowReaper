<?php

declare(strict_types=1);

namespace App;

use Exception;
use JsonException;
use SplFileInfo;
use Symfony\Component\Finder\Finder;

class Mod
{
    private(set) int $id {
        get {
            return $this->id;
        }
    }
    
    private(set) string $title {
        get {
            return $this->title;
        }
    }
    
    public string $dirName {
        get {
            return $this->dirInfo->getBasename();
        }
    }
    
    public string $pakName {
        get {
            return $this->pakInfo->getBasename();
        }
    }
    
    public string $pakDirPath {
        get {
            return $this->pakInfo->getRealPath();
        }
    }
    
    protected array $meta;
    protected SplFileInfo $dirInfo;
    protected SplFileInfo $pakInfo;

    public function __construct(SplFileInfo $modDirInfo)
    {
        $this->dirInfo = $modDirInfo;
        
        $this->parsePakDir();
        $this->parseMeta();
    }
    
    protected function parseMeta(): void
    {
        $modioJsonPath = $this->dirInfo->getRealPath() . DS . 'modio.json';
        if (!file_exists($modioJsonPath)) {
            throw new Exception("File not found: $modioJsonPath");
        }
        
        try {
            $this->meta = json_decode(
                json: file_get_contents($modioJsonPath),
                associative: true,
                flags: JSON_THROW_ON_ERROR,
            );
        } catch (JsonException $e) {
            throw new Exception("Failed to decode $modioJsonPath: {$e->getMessage()}");
        }
        
        $this->id = (int) $this->meta['id'];
        $this->title = $this->meta['name'];
    }
    
    protected function parsePakDir(): void
    {
        $dirs = new Finder()
            ->directories()
            ->in($this->dirInfo->getRealPath())
            ->depth(0);
        
        if (!$dirs->hasResults()) {
            throw new Exception("No subdirs found in {$this->dirInfo->getRealPath()}");
        }
        
        $iterator = $dirs->getIterator();
        $iterator->rewind();
        
        $this->pakInfo = $iterator->current();
    }
}
