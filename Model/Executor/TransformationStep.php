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
 * TransformationStep
 *
 * @copyright  Copyright (c) 2019 TechDivision GmbH (http://www.techdivision.com)
 * @link       http://www.techdivision.com/
 * @author     Team Allstars <allstars@techdivision.com>
 */
class TransformationStep extends AbstractExecutor
{
    /**
     * Process
     *
     * @param StepInterface $step
     * @throws ExecutorException
     * @throws FileSystemException
     */
    public function process(StepInterface $step): void
    {
        // the working directory (including the pipeline ID)
        $workingDir = $step->getWorkingDirectory();

        // prepare the pipeline working base directory, by cutting off the pipeline ID
        $baseDir = str_replace(sprintf('/%s', $step->getPipelineId()), null, $workingDir);

        $this->runCmd(
            [
                '/usr/bin/env php',
                $this->getAbsolutePath('vendor/bin/import-m2if'),
                $step->getArgumentValueByKey('command'),
                $step->getArgumentValueByKey('operation'),
                '--serial="' . $step->getPipelineId() . '"',
                '--archive-artefacts=false',
                '--configuration=' . $this->getAbsolutePath($step->getArgumentValueByKey('configuration')),
                '--params=\'' . json_encode($this->configurationProvider->getConfigByStepName($step->getName())) . '\'',
                '--source-dir="' . $this->getAbsolutePath($baseDir) . '"',
                '--target-dir="' . $this->getAbsolutePath($baseDir) . '"',
                '--pid-filename="' . $this->pidFilePathProvider->get([$step->getName()]) . '"',
                '--installation-dir="' . $this->getRootDir(). '"',
                '--debug-mode=1',
                '2>&1',
            ]
        );
    }
}
