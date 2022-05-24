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

namespace Hoegl\PipelinesImport\Block\System\Config\Form\Field\FieldArray;

use Hoegl\PipelinesImport\Block\System\Config\Form\Field\WebsiteCodeSelect;
use Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\LocalizedException;

/**
 * CountryCodeMapping
 *
 * @copyright  Copyright (c) 2019 TechDivision GmbH (http://www.techdivision.com)
 * @link       http://www.techdivision.com/
 * @author     Team Allstars <allstars@techdivision.com>
 */
class CountryCodeMapping extends AbstractFieldArray
{
    /**
     * @var WebsiteCodeSelect
     */
    private $websiteCodeRenderer;

    /**
     * Retrieve store code column renderer
     *
     * @return WebsiteCodeSelect
     * @throws LocalizedException
     */
    private function getWebsiteCodeRenderer(): WebsiteCodeSelect
    {
        if (!$this->websiteCodeRenderer) {
            $this->websiteCodeRenderer = $this->getLayout()->createBlock(
                WebsiteCodeSelect::class,
                '',
                ['data' => ['is_render_to_js_template' => true]]
            );
            $this->websiteCodeRenderer->setClass('website_code_select');
        }
        return $this->websiteCodeRenderer;
    }

    /**
     * Prepare to render
     *
     * @return void
     * @throws LocalizedException
     */
    protected function _prepareToRender(): void
    {
        $this->addColumn('country_code', ['label' => __('Country Code')]);
        $this->addColumn(
            'website_code',
            ['label' => __('Website'), 'renderer' => $this->getWebsiteCodeRenderer()]
        );
        $this->_addAfter = false;
        $this->_addButtonLabel = __('Add Mapping Value');
    }

    /**
     * Prepare existing row data object
     *
     * @param DataObject $row
     * @return void
     * @throws LocalizedException
     */
    protected function _prepareArrayRow(DataObject $row): void
    {
        $optionExtraAttr = [];
        $websiteCodes = $row->getData('website_code');
        if (is_array($websiteCodes)) {
            foreach ($websiteCodes as $websiteCode) {
                $optionExtraAttr['option_' . $this->getWebsiteCodeRenderer()->calcOptionHash($websiteCode)]
                    = 'selected="selected"';
            }
        }
        $row->setData('option_extra_attrs', $optionExtraAttr);
    }
}
