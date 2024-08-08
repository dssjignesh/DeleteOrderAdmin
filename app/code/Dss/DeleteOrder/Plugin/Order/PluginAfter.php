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
namespace Dss\DeleteOrder\Plugin\Order;

use Dss\DeleteOrder\Plugin\PluginAbstract;
use Magento\Authorization\Model\Acl\AclRetriever;
use Magento\Backend\Model\Auth\Session;
use Magento\Backend\Helper\Data;

class PluginAfter extends PluginAbstract
{
    /**
     * PluginAfter constructor.
     *
     * @param AclRetriever $aclRetriever
     * @param Session $authSession
     * @param Data $data
     */
    public function __construct(
        AclRetriever $aclRetriever,
        Session $authSession,
        protected Data $data
    ) {
        parent::__construct($aclRetriever, $authSession);
    }

    /**
     * Adds a delete button to the order view page if the user has the necessary permissions.
     *
     * @param \Magento\Sales\Block\Adminhtml\Order\View $subject
     * @param mixed $result
     * @return mixed
     */
    public function afterGetBackUrl(\Magento\Sales\Block\Adminhtml\Order\View $subject, $result)
    {
        if ($this->isAllowedResources()) {
            $params = $subject->getRequest()->getParams();
            $message = __('Are you sure you want to do this?');
            if ($subject->getRequest()->getFullActionName() == 'sales_order_view') {
                $subject->addButton(
                    'bss-delete',
                    [
                        'label' => __('Delete'),
                        'onclick' => 'confirmSetLocation(\'' . $message . '\',\'' .
                            $this->getDeleteUrl($params['order_id']) . '\')', 'class' => 'bss-delete'],
                    -1
                );
            }
        }
        return $result;
    }

    /**
     * Get the URL for deleting a order.
     *
     * @param string $orderId
     * @return string
     */
    public function getDeleteUrl($orderId): string
    {
        return $this->data->getUrl(
            'deleteorder/delete/order',
            [
                'order_id' => $orderId
            ]
        );
    }
}
