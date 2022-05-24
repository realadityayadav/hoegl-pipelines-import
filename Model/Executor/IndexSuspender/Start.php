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

namespace Hoegl\PipelinesImport\Model\Executor\IndexSuspender;

use TechDivision\IndexSuspender\Model\DeltaIndexSuspender;
use TechDivision\IndexSuspender\Model\SuspendManager;
use TechDivision\ProcessPipelines\Api\ExecutorLoggerInterface;
use TechDivision\ProcessPipelines\Api\StepInterface;
use TechDivision\ProcessPipelines\Model\Executor\AbstractExecutor;

/**
 * Start
 *
 * @copyright  Copyright (c) 2019 TechDivision GmbH (http://www.techdivision.com)
 * @link       http://www.techdivision.com/
 * @author     Team Allstars <allstars@techdivision.com>
 */
class Start extends AbstractExecutor
{
    const PREFIX_PIPELINE_SUSPENDER = 'PIPELINE_';

    const SUSPENDER_START_LOG_MESSAGE  = "Start Suspender for Pipeline: %s";
    const SUSPENDER_STOP_LOG_MESSAGE  = "Stop Suspender for Pipeline: %s";
    const SUSPENDER_FLUSH_LOG_MESSAGE  = "Flush MViews for Pipeline: %s";
    const SUSPENDER_WARNING_STOP_MESSAGE  = "No Suspender found for Pipeline: %s";

    /** @var  SuspendManager */
    protected $suspendManager;

    /** @var  DeltaIndexSuspender */
    protected $indexSuspender;

    /**
     * AbstractImport constructor.
     *
     * @param ExecutorLoggerInterface $logger
     * @param SuspendManager $suspendManager
     */
    public function __construct(
        ExecutorLoggerInterface $logger,
        SuspendManager $suspendManager
    ) {
        parent::__construct($logger);
        $this->suspendManager = $suspendManager;
        $this->indexSuspender = $suspendManager->getNewDeltaIndexSuspender();
    }

    /**
     * Process
     *
     * @param StepInterface $step
     * @return void
     * @throws \Exception
     */
    public function process(StepInterface $step): void
    {
        $this->indexSuspender->suspendExternal(self::PREFIX_PIPELINE_SUSPENDER . $step->getPipelineId());

        $successMessage = sprintf(self::SUSPENDER_START_LOG_MESSAGE, $step->getPipelineId());
        $this->logger->info($successMessage);
    }
}
