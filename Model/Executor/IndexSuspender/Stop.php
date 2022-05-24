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

use Magento\Framework\Indexer\StateInterface;
use Magento\Indexer\Model\Indexer\CollectionFactory;
use Magento\Indexer\Model\Indexer;
use TechDivision\IndexSuspender\Model\SuspendManager;
use TechDivision\ProcessPipelines\Api\ExecutorLoggerInterface;
use TechDivision\ProcessPipelines\Api\StepInterface;

/**
 * Stop
 *
 * @copyright  Copyright (c) 2019 TechDivision GmbH (http://www.techdivision.com)
 * @link       http://www.techdivision.com/
 * @author     Team Allstars <allstars@techdivision.com>
 */
class Stop extends Start
{
    /**
     * @var CollectionFactory
     */
    private $indexerCollectionFactory;

    /**
     * @param ExecutorLoggerInterface $logger
     * @param SuspendManager $suspendManager
     * @param CollectionFactory $indexerCollectionFactory
     */
    public function __construct(
        ExecutorLoggerInterface $logger,
        SuspendManager $suspendManager,
        CollectionFactory $indexerCollectionFactory
    ) {
        parent::__construct($logger, $suspendManager);
        $this->indexerCollectionFactory = $indexerCollectionFactory;
    }

    /**
     * @inheritdoc
     * @throws \Exception
     */
    public function process(StepInterface $step): void
    {
        $suspender = $this->suspendManager->getDeltaIndexSuspenderByExternalKey(
            self::PREFIX_PIPELINE_SUSPENDER . $step->getPipelineId()
        );

        if ($suspender && $suspender->getId() > 0) {
            $suspender->resume();
            $successMessage = sprintf(self::SUSPENDER_STOP_LOG_MESSAGE, $step->getPipelineId());
            $this->logger->info($successMessage);
        } else {
            $successMessage = sprintf(self::SUSPENDER_WARNING_STOP_MESSAGE, $step->getPipelineId());
            $this->logger->warning($successMessage);
        }
    }
}
