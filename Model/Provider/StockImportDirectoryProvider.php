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

namespace Hoegl\PipelinesImport\Model\Provider;

use Hoegl\Pipelines\Model\Provider\AbstractImportDirectoryProvider;

/**
 * StockImportDirectoryProvider
 *
 * @copyright  Copyright (c) 2019 TechDivision GmbH (http://www.techdivision.com)
 * @link       http://www.techdivision.com/
 * @author     Team Allstars <allstars@techdivision.com>
 */
class StockImportDirectoryProvider extends AbstractImportDirectoryProvider
{
    /**
     * @inheritdoc
     */
    protected function getConfigPath(): string
    {
        return 'import/stock_import/directory';
    }
}
