<?php

declare(strict_types= 1);

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
namespace Dss\DeleteOrder\Plugin;

use Magento\Authorization\Model\Acl\AclRetriever;
use Magento\Backend\Model\Auth\Session;

class PluginAbstract
{
    /**
     * PluginAbstract constructor.
     *
     * @param AclRetriever $aclRetriever
     * @param Session $authSession
     */
    public function __construct(
        protected AclRetriever $aclRetriever,
        protected Session $authSession
    ) {
    }

    /**
     * Checks if the current user is allowed to access certain resources.
     *
     * @return bool
     */
    public function isAllowedResources(): bool
    {
        $user = $this->authSession->getUser();
        $role = $user->getRole();
        $resources = $this->aclRetriever->getAllowedResourcesByRole($role->getId());
        if (in_array("Magento_Backend::all", $resources) || in_array("Dss_DeleteOrder::delete_order", $resources)) {
            return true;
        }
        return false;
    }
}
