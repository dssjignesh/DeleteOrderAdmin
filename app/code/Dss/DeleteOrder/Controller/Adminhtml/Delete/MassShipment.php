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

use Dss\DeleteOrder\Model\Shipment\Delete;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Magento\Backend\App\Action\Context;
use Magento\Sales\Model\Order\Shipment;
use Magento\Ui\Component\MassAction\Filter;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory;
use Magento\Sales\Model\ResourceModel\Order\Shipment\CollectionFactory as ShipmentCollection;
use \Magento\Sales\Controller\Adminhtml\Order\AbstractMassAction;

class MassShipment extends AbstractMassAction
{
    /**
     * @param Context $context
     * @param Filter $filter
     * @param CollectionFactory $shipmentCollection
     * @param Shipment $shipment
     * @param Delete $delete
     */
    public function __construct(
        Context $context,
        Filter $filter,
        CollectionFactory $collectionFactory,
        protected ShipmentCollection $shipmentCollection,
        protected Shipment $shipment,
        protected Delete $delete
    ) {
        parent::__construct($context, $filter);
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * Mass action
     *
     * @param AbstractCollection $collection
     * @return ResponseInterface|Redirect|ResultInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    protected function massAction(AbstractCollection $collection)
    {
        $params = $this->getRequest()->getParams();
        $selected = [];
        $shipmentCollection = $this->shipmentCollection->create();
        $collectionShipment = $this->filter->getCollection($shipmentCollection);
        foreach ($collectionShipment as $shipment) {
            array_push($selected, $shipment->getId());
        }
        if ($selected) {
            foreach ($selected as $shipmentId) {
                $shipment = $this->shipment->load($shipmentId);
                try {
                    $order = $this->delete->deleteShipment($shipmentId);
                    $this->messageManager->addSuccessMessage(
                        __(
                            'Successfully deleted shipment #%1.',
                            $shipment->getIncrementId()
                        )
                    );
                } catch (\Exception $e) {
                    $this->messageManager->addErrorMessage(
                        __(
                            'Error delete shipment #%1.',
                            $shipment->getIncrementId()
                        )
                    );
                }
            }
        }

        $resultRedirect = $this->resultRedirectFactory->create();
        $resultRedirect->setPath('sales/shipment/');
        if ($params['namespace'] == 'sales_order_view_shipment_grid' && isset($order)) {
            $resultRedirect->setPath('sales/order/view', ['order_id' => $order->getId()]);
        } else {
            $resultRedirect->setPath('sales/shipment/');
        }
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
