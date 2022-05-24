<?php
/**
 * Copyright (c) 2021 TechDivision GmbH
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

use Exception;
use Hoegl\PipelinesImport\Api\FullReindexConfigInterface;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\Shell;
use Magento\Framework\Exception\LocalizedException;
use Magento\Indexer\Model\Processor;
use TechDivision\IndexSuspender\Model\SuspendManager;
use TechDivision\PacemakerImportBase\Model\Executor\IndexSuspender\Stop;
use TechDivision\ProcessPipelines\Api\ExecutorLoggerInterface;
use TechDivision\ProcessPipelines\Api\StepInterface;
use TechDivision\ProcessPipelines\Exception\ExecutorException;
use TechDivision\ProcessPipelines\Model\Executor\Reindex;

/**
 * @copyright  Copyright (c) 2021 TechDivision GmbH <info@techdivision.com> - TechDivision GmbH
 * @link       https://www.techdivision.com/
 * @author     Allstars Team <allstars@techdivision.com>
 */
class ConfiguredReindex extends Reindex
{
    /** @var FullReindexConfigInterface */
    private $fullReindex;

    /** @var SuspendManager */
    private $suspendManager;

    /** @var array */
    private $indices;

    /** @var Processor */
    private $processor;

    /**
     * @param ExecutorLoggerInterface $logger
     * @param DirectoryList $directoryList
     * @param FullReindexConfigInterface $fullReindex
     * @param SuspendManager $suspendManager
     * @param Processor $processor
     * @param array $indices
     * @param Shell|null $shell
     */
    public function __construct(
        ExecutorLoggerInterface $logger,
        DirectoryList $directoryList,
        FullReindexConfigInterface $fullReindex,
        SuspendManager $suspendManager,
        Processor $processor,
        array $indices = ['inventory'],
        Shell $shell = null
    ) {
        parent::__construct($logger, $directoryList);
        $this->fullReindex = $fullReindex;
        $this->suspendManager = $suspendManager;
        $this->indices = $indices;
        $this->processor = $processor;
    }

    /**
     * @inheritdoc
     * @param StepInterface $step
     * @throws LocalizedException
     * @throws ExecutorException
     * @throws Exception
     */
    public function process(StepInterface $step): void
    {
        if ($this->fullReindex->useFullIndex()) {
            $this->clearMviewDelta($step);
            $indexes = preg_split('/[,\s;]/', $step->getArgumentValueByKey(self::ARG_INDEXES) ?? '');
            foreach ($this->indices as $indexName) {
                if (!in_array($indexName, $indexes)) {
                    $indexes[] = $indexName;
                }
            }
            $step->addArgument(self::ARG_INDEXES, implode(',', $indexes));
            parent::process($step);
        } else {
            $this->logger->info('Delta-Index preferred');
            $this->getLogger()->info('Execute: update mview');
            $this->processor->updateMview();
            $this->getLogger()->info('Execute: reindex all invalid');
            $this->processor->reindexAllInvalid();
        }
    }

    /**
     * @param StepInterface $step
     * @throws Exception
     */
    private function clearMviewDelta(StepInterface $step): void
    {
        $this->suspendManager->flushAll();
        $successMessage = sprintf(Stop::SUSPENDER_FLUSH_LOG_MESSAGE, $step->getPipelineId());
        $this->logger->info($successMessage);
    }
}
