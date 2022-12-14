<?php

declare(strict_types=1);

namespace IXCSoft\TreinamentoDocker\Application\Application;

use SplFileInfo;

abstract class Config
{
    protected SplFileInfo $configDir;

    public function __construct(SplFileInfo $configDir)
    {
        $this->configDir = $configDir;
    }

    public abstract function getConfig(): array;

    protected function parseConfigFile(string $configFile): array
    {
        $ymlFile = $this->configDir->getPathname() . '/' . $configFile;
        $fileConfig = [];
        if (file_exists($ymlFile)) {
            $fileConfig = yaml_parse_file($ymlFile);
        }
        return $fileConfig;
    }
}
