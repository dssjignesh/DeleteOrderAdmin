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
namespace Dss\DeleteOrder\Controller\Adminhtml\Delete;

use Magento\Backend\App\Action;
use Magento\Sales\Model\Order as SalesOrder;
use Dss\DeleteOrder\Model\Order\Delete;

class Order extends Action
{
    /**
     * Order constructor.
     *
     * @param Action\Context $context
     * @param SalesOrder $order
     * @param Delete $delete
     */
    public function __construct(
        Action\Context $context,
        protected SalesOrder $order,
        protected Delete $delete
    ) {
        parent::__construct($context);
    }

    /**
     * Execute the action
     *
     * @return ResponseInterface|Redirect|ResultInterface
     */
    public function execute()
    {
        $orderId = (string)$this->getRequest()->getParam('order_id');
        $order = $this->order->load($orderId);
        $incrementId = $order->getIncrementId();
        try {
            $this->delete->deleteOrder($orderId);
            $this->messageManager->addSuccessMessage(__('Successfully deleted order #%1.', $incrementId));
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(__('Error delete order #%1.', $incrementId));
        }
        $resultRedirect = $this->resultRedirectFactory->create();
        $resultRedirect->setPath('sales/order/');
        return $resultRedirect;
    }

    /**
     * Check permission via ACL resource
     *
     * @retun bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Dss_DeleteOrder::delete_order');
    }
}
