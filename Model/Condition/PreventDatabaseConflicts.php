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

use Magento\Framework\Api\SearchCriteriaBuilder;
use TechDivision\ProcessPipelines\Api\StepConditionInterface;
use TechDivision\ProcessPipelines\Api\StepInterface;
use TechDivision\ProcessPipelines\Model\Query\GetStepsList;

/**
 * PreventDatabaseConflicts
 *
 * @copyright  Copyright (c) 2019 TechDivision GmbH (http://www.techdivision.com)
 * @link       http://www.techdivision.com/
 * @author     Team Allstars <allstars@techdivision.com>
 */
class PreventDatabaseConflicts implements StepConditionInterface
{
    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var GetStepsList
     */
    private $getStepsList;

    /**
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param GetStepsList $getStepsList
     */
    public function __construct(
        SearchCriteriaBuilder $searchCriteriaBuilder,
        GetStepsList $getStepsList
    ) {
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->getStepsList = $getStepsList;
    }

    /**
     * Is ready
     *
     * @param StepInterface $step
     * @return bool
     */
    public function isReady(StepInterface $step): bool
    {
        $searchCriteria = $this->searchCriteriaBuilder
            ->addFilter(
                StepInterface::FIELD_STATUS,
                [
                    StepInterface::STATUS_RUNNING,
                    StepInterface::STATUS_ENQUEUED,
                ],
                'in'
            )
            ->addFilter(
                StepInterface::FIELD_NAME,
                [
                    'stock_import_product',
                    'price_import',
                    'price_b2b_import',
                    'reindex',
                ],
                'in'
            )
            ->addFilter(
                StepInterface::FIELD_ID,
                $step->getId(),
                'neq'
            )
            ->create();

        return $this->getStepsList->execute($searchCriteria)->getTotalCount() === 0;
    }
}
