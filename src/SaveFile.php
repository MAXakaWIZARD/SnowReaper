<?php

declare(strict_types=1);

namespace App;

use InvalidArgumentException;

class SaveFile
{
    protected const DIFFICULTY_NORMAL = 0;
    protected const DIFFICULTY_HARD = 1;
    protected const DIFFICULTY_PLUS = 2;
    
    protected const DIFFICULTY_MAP = [
        self::DIFFICULTY_NORMAL => 'Normal',
        self::DIFFICULTY_HARD => 'Hard',
        self::DIFFICULTY_PLUS => 'Plus',
    ];
    
    private(set) int $slot {
        get {
            return $this->slot;
        }
    }
    
    private(set) string $dir {
        get {
            return $this->dir;
        }
    }
    
    public string $fileName {
        get {
            return sprintf(
                'completesave%s.cfg',
                $this->slot === 1 ? '' : $this->slot - 1,
            );
        }
    }
    
    public string $filePath {
        get {
            return "{$this->dir}/{$this->fileName}";
        }
    }
    
    public string $rootNodeName {
        get {
            return sprintf(
                'CompleteSave%s',
                $this->slot === 1 ? '' : $this->slot - 1,
            );
        }
    }
    
    protected object $rawData;

    public function __construct(string $dir, int $slot = 1)
    {
        $this->dir = $dir;
        $this->slot = $slot;
        
        if (!file_exists($this->filePath)) {
            throw new InvalidArgumentException('File not found: ' . $this->filePath);
        }
        
        $this->rawData = json_decode(
            json: trim(file_get_contents($this->filePath)),
            flags: JSON_THROW_ON_ERROR,
        );
    }
    
    public function getData(): object
    {
        return $this->rawData->{$this->rootNodeName}->SslValue;
    }
    
    protected function setData(object $data): void
    {
        $this->rawData->{$this->rootNodeName}->SslValue = $data;
    }
    
    protected function setProfileData(object $profileData): void
    {
        $data = $this->getData();
        $data->persistentProfileData = $profileData;
        
        $this->setData($data);
    }
    
    public function getProfileData(): object
    {
        return $this->getData()->persistentProfileData;
    }
    
    public function getMoney(): int
    {
        return $this->getProfileData()->money ?? 0;
    }
    
    public function isHardMode(): bool
    {
        return (bool) $this->getData()->isHardMode;
    }
    
    public function getDifficulty(): int
    {
        return (int) $this->getData()->gameDifficultyMode;
    }
    
    public function getDifficultyName(): string
    {
        return self::DIFFICULTY_MAP[$this->getDifficulty()] ?? '';
    }
    
    public function getDifficultySettings(): object
    {
        return $this->getData()->gameDifficultySettings;
    }
    
    public function addMoney(int $amount): void
    {
        $profileData = $this->getProfileData();
        $profileData->money += $amount;
        
        $this->setProfileData($profileData);
    }
    
    public function setHardMode(bool $enabled): void
    {
        $data = $this->getData();
        $data->isHardMode = $enabled;
        
        if ($enabled) {
            $data->gameDifficultyMode = self::DIFFICULTY_HARD;
        } else {
            $data->gameDifficultyMode = self::DIFFICULTY_PLUS;
        }
        
        $this->setData($data);
    }
    
    public function write(): void
    {
        $encodedData = json_encode(
            value: $this->rawData, 
            flags: JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT,
        );
        
        file_put_contents($this->filePath, $encodedData . chr(0x00) . chr(0x0a));
    }
}
