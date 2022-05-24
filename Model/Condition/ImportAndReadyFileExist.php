<?php
/**
 * Copyright (c) 2019 TechDivision GmbH
 * All rights reserved
 *
 * This product includes proprietary software developed at TechDivision GmbH, Germany
 * For more information see http://www.techdivision.com/
 *
 * To obtain a valid license for using this software please contact us at
 * license@techdivision.com
 */
declare(strict_types=1);

namespace Hoegl\PipelinesImport\Model\Condition;

use Hoegl\Pipelines\Model\Provider\ImportDirectoryProviderInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use TechDivision\ProcessPipelines\Api\ArgumentProviderInterface;
use TechDivision\ProcessPipelines\Api\PipelineConditionInterface;

/**
 * ImportAndReadyFileExist
 *
 * @copyright  Copyright (c) 2019 TechDivision GmbH (http://www.techdivision.com)
 * @link       http://www.techdivision.com/
 * @author     Team Allstars <allstars@techdivision.com>
 */
class ImportAndReadyFileExist implements PipelineConditionInterface, ArgumentProviderInterface
{
    /**
     * @var ImportDirectoryProviderInterface
     */
    private $importSourceDirectoryProvider;

    /**
     * @var string
     */
    private $filePatternConfigPath;

    /**
     * @var array
     */
    private $arguments = [];

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @param ImportDirectoryProviderInterface $importSourceDirectoryProvider
     * @param ScopeConfigInterface $scopeConfig
     * @param string $filePatternConfigPath
     */
    public function __construct(
        ImportDirectoryProviderInterface $importSourceDirectoryProvider,
        ScopeConfigInterface $scopeConfig,
        string $filePatternConfigPath = ''
    ) {
        $this->importSourceDirectoryProvider = $importSourceDirectoryProvider;
        $this->scopeConfig = $scopeConfig;
        $this->filePatternConfigPath = $filePatternConfigPath;
    }

    /**
     * Check if import and ready file are present.
     *
     * @param array $pipelineConfiguration
     * @return bool
     */
    public function isReady(array $pipelineConfiguration): bool
    {
        return $this->searchForFilePairs();
    }

    /**
     * Get path pattern
     *
     * @return string
     */
    private function getPathPattern(): string
    {
        return implode(DIRECTORY_SEPARATOR, [
            $this->importSourceDirectoryProvider->get(),
            $this->scopeConfig->getValue($this->filePatternConfigPath)
        ]);
    }

    /**
     * Search for file pairs
     *
     * @return bool
     */
    private function searchForFilePairs(): bool
    {
        $files = glob($this->getPathPattern());
        foreach ($files as $file) {
            foreach (['CSV', 'csv'] as $suffix) {
                $baseName = basename($file, '.' . $suffix);
                $importPath = dirname($file);
                $readyFilePath = $importPath . DIRECTORY_SEPARATOR . $baseName . '.ready';

                $this->arguments['files'] = [
                    'csv' => $file,
                    'ready' => $readyFilePath
                ];

                if (file_exists($readyFilePath)) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Get arguments
     *
     * @return array
     */
    public function getArguments(): array
    {
        return $this->arguments;
    }
}
