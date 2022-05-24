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
namespace Hoegl\PipelinesImport\Api;

/**
 * @copyright  Copyright (c) 2021 TechDivision GmbH <info@techdivision.com> - TechDivision GmbH
 * @link       https://www.techdivision.com/
 * @author     Allstars Team <allstars@techdivision.com>
 */
interface FullReindexConfigInterface
{
    /**
     * @return bool
     */
    public function useFullIndex(): bool;
}
