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
namespace Hoegl\PipelinesImport\Model\ConfigProvider;

use Hoegl\PipelinesImport\Api\FullReindexConfigInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Mview\ViewInterface;

/**
 * @copyright  Copyright (c) 2021 TechDivision GmbH <info@techdivision.com> - TechDivision GmbH
 * @link       https://www.techdivision.com/
 * @author     Allstars Team <allstars@techdivision.com>
 */
class FullReindexConfig implements FullReindexConfigInterface
{
    /** @var ScopeConfigInterface */
    private $scopeConfig;

    /** @var string */
    private $reindexConfigPath;

    /** @var ViewInterface */
    private $view;

    /**
     * @param ScopeConfigInterface $scopeConfig
     * @param ViewInterface $view
     * @param string $reindexConfigPath
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        ViewInterface $view,
        string $reindexConfigPath = 'import/stock_import/threshold'
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->view = $view;
        $this->reindexConfigPath = $reindexConfigPath;
    }

    /**
     * Get configured threshold and decide whether to use full or delta index
     * (Threshold smaller: Full Index; Threshold greater: Delta Index)
     *
     * @return bool
     */
    public function useFullIndex(): bool
    {
        $configuredThreshold = (int)$this->scopeConfig->getValue($this->reindexConfigPath);

        $inventoryView = $this->view->load('inventory');
        $state = $inventoryView->getState();
        $changelog = $inventoryView->getChangelog();

        $element = $changelog->getList($state->getVersionId(), $changelog->getVersion());
        return $configuredThreshold <= count($element);
    }
}
