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

namespace Hoegl\PipelinesImport\Model\Executor;

use Magento\Framework\Exception\FileSystemException;
use TechDivision\ProcessPipelines\Api\StepInterface;
use TechDivision\ProcessPipelines\Exception\ExecutorException;
use Hoegl\Pipelines\Model\Executor\AbstractExecutor;

/**
 * ImportStep
 *
 * @copyright  Copyright (c) 2019 TechDivision GmbH (http://www.techdivision.com)
 * @link       http://www.techdivision.com/
 * @author     Team Allstars <allstars@techdivision.com>
 */
class ImportStep extends AbstractExecutor
{
    /**
     * @inheritdoc
     *
     * @throws FileSystemException
     */
    public function process(StepInterface $step): void
    {
        $this->createOkFile($step);
        $this->runImport($step);
    }

    /**
     * Create ok file
     *
     * @param StepInterface $step
     * @throws ExecutorException
     * @throws FileSystemException
     */
    private function createOkFile(StepInterface $step): void
    {
        $this->runCmd([
            '/usr/bin/env php',
            $this->getAbsolutePath('vendor/bin/import-pro'),
            'import:create:ok-file',
            '--configuration=' . $this->getAbsolutePath($step->getArgumentValueByKey('configuration')),
            '--source-dir="' . $this->getAbsolutePath($step->getWorkingDirectory()) . '"',
            '--pid-filename="' . $this->pidFilePathProvider->get([$step->getName()]) . '"',
            '2>&1',
        ]);
    }

    /**
     * Run import
     *
     * @param StepInterface $step
     * @throws ExecutorException
     * @throws FileSystemException
     */
    private function runImport(StepInterface $step): void
    {
        $this->runCmd([
            '/usr/bin/env php',
            $this->getAbsolutePath('vendor/bin/import-pro'),
            $step->getArgumentValueByKey('command'),
            $step->getArgumentValueByKey('operation'),
            '--configuration=' . $this->getAbsolutePath($step->getArgumentValueByKey('configuration')),
            '--source-dir="' . $this->getAbsolutePath($step->getWorkingDirectory()) . '"',
            '--target-dir="' . $this->getAbsolutePath($step->getWorkingDirectory()) . '"',
            '--pid-filename="' . $this->pidFilePathProvider->get([$step->getName()]) . '"',
            '2>&1',
        ]);
    }
}
