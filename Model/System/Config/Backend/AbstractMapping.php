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

namespace Hoegl\PipelinesImport\Model\System\Config\Backend;

use Magento\Framework\App\Cache\TypeListInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Config\Value;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Math\Random;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Registry;
use Magento\Framework\Serialize\Serializer\Json;

/**
 * Abstract AbstractMapping
 *
 * @copyright  Copyright (c) 2019 TechDivision GmbH (http://www.techdivision.com)
 * @link       http://www.techdivision.com/
 * @author     Team Allstars <allstars@techdivision.com>
 */
abstract class AbstractMapping extends Value
{
    /**
     * @var Json
     */
    protected $serializer;

    /**
     * @var Random
     */
    protected $mathRandom;

    /**
     * @param Context $context
     * @param Registry $registry
     * @param ScopeConfigInterface $config
     * @param TypeListInterface $cacheTypeList
     * @param Json $serializer
     * @param Random $mathRandom
     * @param AbstractResource|null $resource
     * @param AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        ScopeConfigInterface $config,
        TypeListInterface $cacheTypeList,
        Json $serializer,
        Random $mathRandom,
        ?AbstractResource $resource = null,
        ?AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct($context, $registry, $config, $cacheTypeList, $resource, $resourceCollection, $data);
        $this->serializer = $serializer;
        $this->mathRandom = $mathRandom;
    }

    /**
     * Process data after load
     *
     * @return void
     * @throws LocalizedException
     */
    protected function _afterLoad(): void
    {
        $value = $this->getValue();
        $value = $this->decode($value);
        $this->setValue($value);
    }

    /**
     * Prepare data before save
     *
     * @return void
     */
    public function beforeSave(): void
    {
        $value = $this->getValue();
        $value = $this->encode((array)$value);
        $this->setValue($value);
    }

    /**
     * Decode configuration string into array
     *
     * @param string $value
     * @return array
     * @throws LocalizedException
     */
    abstract protected function decode(string $value): array;

    /**
     * Encode array into configuration string value
     *
     * @param array $array
     * @return string
     */
    abstract protected function encode(array $array): string;
}
