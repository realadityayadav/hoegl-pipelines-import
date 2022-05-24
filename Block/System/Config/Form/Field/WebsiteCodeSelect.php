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

namespace Hoegl\PipelinesImport\Block\System\Config\Form\Field;

use Magento\Framework\View\Element\Context;
use Magento\Framework\View\Element\Html\Select;
use Magento\Store\Api\Data\WebsiteInterface;
use Magento\Store\Model\StoreManager;

/**
 * WebsiteCodeSelect
 *
 * @copyright  Copyright (c) 2019 TechDivision GmbH (http://www.techdivision.com)
 * @link       http://www.techdivision.com/
 * @author     Team Allstars <allstars@techdivision.com>
 */
class WebsiteCodeSelect extends Select
{
    /**
     * Customer groups cache
     *
     * @var array
     */
    private $websiteCodeOptions;

    /**
     * @var StoreManager
     */
    private $storeManager;

    /**
     * @param Context $context
     * @param StoreManager $storeManager
     * @param array $data
     */
    public function __construct(
        Context $context,
        StoreManager $storeManager,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->storeManager = $storeManager;
    }

    /**
     * Get website code options
     *
     * @return array
     */
    private function getWebsiteCodeOptions(): array
    {
        if ($this->websiteCodeOptions === null) {
            $this->websiteCodeOptions = [];
            /** @var WebsiteInterface $website */
            foreach ($this->storeManager->getWebsites() as $website) {
                $this->websiteCodeOptions[$website->getCode()] = $website->getName();
            }
        }

        return $this->websiteCodeOptions;
    }

    /**
     * Sets name for input element
     *
     * @param string $value
     * @return WebsiteCodeSelect
     */
    public function setInputName($value): WebsiteCodeSelect
    {
        return $this->setName($value . '[]');
    }

    /**
     * Render block HTML
     *
     * @return string
     */
    public function _toHtml(): string
    {
        if (!$this->getOptions()) {
            foreach ($this->getWebsiteCodeOptions() as $key => $value) {
                $this->addOption($key, $value);
            }
        }

        $this->setExtraParams('multiple="multiple"');
        return parent::_toHtml();
    }
}
