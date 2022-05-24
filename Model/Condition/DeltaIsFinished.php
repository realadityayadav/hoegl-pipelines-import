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

use Magento\Cron\Model\ResourceModel\Schedule\CollectionFactory;
use Magento\Cron\Model\Schedule;
use TechDivision\IndexSuspender\Model\DeltaIndexSuspender;
use TechDivision\ProcessPipelines\Api\StepConditionInterface;
use TechDivision\ProcessPipelines\Api\StepInterface;

/**
 * DeltaIsFinished class.
 *
 * @copyright  Copyright (c) 2019 TechDivision GmbH (http://www.techdivision.com)
 * @link       http://www.techdivision.com/
 * @author     Team Allstars <allstars@techdivision.com>
 */
class DeltaIsFinished implements StepConditionInterface
{
    /**
     * @var CollectionFactory
     */
    private $scheduleCollection;

    /**
     * @var DeltaIndexSuspender
     */
    private $deltaIndexSuspender;

    /**
     * DeltaIsFinished constructor.
     *
     * @param DeltaIndexSuspender $deltaIndexSuspender
     * @param CollectionFactory $scheduleCollection
     */
    public function __construct(
        DeltaIndexSuspender $deltaIndexSuspender,
        CollectionFactory $scheduleCollection
    ) {
        $this->scheduleCollection = $scheduleCollection;
        $this->deltaIndexSuspender = $deltaIndexSuspender;
    }

    /**
     * Check if delta index is finished.
     *
     * @param StepInterface $step
     *
     * @return bool
     */
    public function isReady(StepInterface $step): bool
    {
        $collection = $this->scheduleCollection->create()
            ->addFieldToFilter('status', Schedule::STATUS_RUNNING)
            ->addFieldToFilter('job_code', ['in' => $this->deltaIndexSuspender->getJobCodesToSuspend()]);
        return $collection->count() === 0;
    }
}
