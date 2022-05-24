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

/**
 * Map country codes to ISO country codes
 *
 * @copyright  Copyright (c) 2019 TechDivision GmbH (http://www.techdivision.com)
 * @link       http://www.techdivision.com/
 * @author     Team Allstars <allstars@techdivision.com>
 */
class CountryMapping extends AbstractMapping
{
    /**
     * @inheritdoc
     */
    protected function decode(string $value): array
    {
        $data = $this->serializer->unserialize($value);
        $result = [];
        foreach ($data as $countryCode => $isoCode) {
            $result[$this->mathRandom->getUniqueHash('_')] = [
                'country_code' => $countryCode,
                'iso_code' => $isoCode,
            ];
        }
        return $result;
    }

    /**
     * @inheritdoc
     */
    protected function encode(array $value): string
    {
        $result = [];
        foreach ($value as $key => $data) {
            if (empty($key) || empty($data)) {
                continue;
            }

            $countryCode = $data['country_code'] ?? '';
            $isoCode = $data['iso_code'] ?? '';
            $result[$countryCode] = $isoCode;
        }
        return $this->serializer->serialize($result);
    }
}
