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

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Magento\Backend\App\Action\Context;
use Magento\Ui\Component\MassAction\Filter;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory;
use Magento\Sales\Api\OrderManagementInterface;
use Dss\DeleteOrder\Model\Order\Delete as DeleteModel;

class MassOrder extends \Magento\Sales\Controller\Adminhtml\Order\AbstractMassAction
{
    /**
     * MassOrder constructor.
     * @param Context $context
     * @param Filter $filter
     * @param CollectionFactory $collectionFactory
     * @param OrderManagementInterface $orderManagement
     * @param \Dss\DeleteOrder\Model\Order\Delete $delete
     */
    public function __construct(
        Context $context,
        Filter $filter,
        CollectionFactory $collectionFactory,
        protected OrderManagementInterface $orderManagement,
        protected DeleteModel $delete
    ) {
        parent::__construct($context, $filter);
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * MassAction function
     *
     * @param AbstractCollection $collection
     * @return ResponseInterface|Redirect|ResultInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    protected function massAction(AbstractCollection $collection)
    {
        $orderCollection = $this->collectionFactory->create();
        $collectionInvoice = $this->filter->getCollection($orderCollection);

        foreach ($collectionInvoice as $order) {
            $orderId = (string)$order->getId();
            $incrementId = $order->getIncrementId();
            try {
                $this->delete->deleteOrder($orderId);
                $this->messageManager->addSuccessMessage(__('Successfully deleted order #%1.', $incrementId));
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage(__('Error delete order #%1.', $incrementId));
            }
        }
        $resultRedirect = $this->resultRedirectFactory->create();
        $resultRedirect->setPath('sales/order/');
        return $resultRedirect;
    }

    /**
     * Check permission via ACL resource
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Dss_DeleteOrder::delete_order');
    }
}
