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
use Magento\Sales\Model\Order\Shipment as SalesShipment;
use Dss\DeleteOrder\Model\Shipment\Delete;

class Shipment extends Action
{
    /**
     * Shipment constructor.
     *
     * @param Action\Context $context
     * @param SalesShipment $shipment
     * @param Delete $delete
     */
    public function __construct(
        Action\Context $context,
        protected SalesShipment $shipment,
        protected Delete $delete
    ) {
        parent::__construct($context);
    }

    /**
     * Execute the action
     *
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\Result\Redirect|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $shipmentId = $this->getRequest()->getParam('shipment_id');
        $shipment = $this->shipment->load($shipmentId);
        try {
            $this->delete->deleteShipment($shipmentId);
            $this->messageManager->addSuccessMessage(__('
                Successfully deleted shipment #%1.', $shipment->getIncrementId()));
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(__('Error delete shipment #%1.', $shipment->getIncrementId()));
        }
        $resultRedirect = $this->resultRedirectFactory->create();
        $resultRedirect->setPath('sales/shipment/');
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
