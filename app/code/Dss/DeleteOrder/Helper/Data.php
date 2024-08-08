<?php

declare(strict_types=1);

/**
 * Digit Software Solutions.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 *
 * @category  Dss
 * @package   Dss_DeleteOrder
 * @author    Extension Team
 * @copyright Copyright (c) 2024 Digit Software Solutions. ( https://digitsoftsol.com )
 */
namespace Dss\DeleteOrder\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\DeploymentConfig;
use Magento\Framework\Config\ConfigOptionsListConstants;
use Magento\Framework\App\Helper\Context;

class Data extends AbstractHelper
{
    /**
     * Data constructor.
     *
     * @param Context $context
     * @param DeploymentConfig $deploymentConfig
     */
    public function __construct(
        Context $context,
        protected DeploymentConfig $deploymentConfig
    ) {
        parent::__construct($context);
    }

    /**
     * Get the table name with prefix if configured
     *
     * @param mixed $name
     * @return bool|string|null
     */
    public function getTableName($name = null)
    {
        if ($name === null) {
            return false;
        }
        $tableName = $name;
        $tablePrefix = (string)$this->deploymentConfig->get(
            ConfigOptionsListConstants::CONFIG_PATH_DB_PREFIX
        );
        if ($tablePrefix) {
            $tableName = $tablePrefix . $name;
        }
        return $tableName;
    }
}
