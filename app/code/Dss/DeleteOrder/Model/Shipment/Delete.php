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
namespace Dss\DeleteOrder\Model\Shipment;

use Magento\Framework\App\ResourceConnection;
use Dss\DeleteOrder\Helper\Data;
use Magento\Sales\Model\Order\Shipment;
use Magento\Sales\Model\Order;

class Delete
{
    /**
     * Delete constructor.
     * @param ResourceConnection $resource
     * @param Data $data
     * @param Shipment $shipment
     * @param Order $order
     */
    public function __construct(
        protected ResourceConnection $resource,
        protected Data $data,
        protected Shipment $shipment,
        protected Order $order
    ) {
    }

    /**
     * Delete the shipment by Id and revert the odrer state.
     *
     * @param string $shipmentId
     * @return Order
     * @throws \Exception
     */
    public function deleteShipment(string $shipmentId)
    {
        $connection = $this->resource->getConnection(ResourceConnection::DEFAULT_CONNECTION);
        $shipmentTable = $connection->getTableName($this->data->getTableName('sales_shipment'));
        $shipmentGridTable = $connection->getTableName($this->data->getTableName('sales_shipment_grid'));
        $shipment = $this->shipment->load($shipmentId);
        $orderId = $shipment->getOrder()->getId();
        $order = $this->order->load($orderId);
        $orderItems = $order->getAllItems();
        $shipmentItems = $shipment->getAllItems();

        // Revert item in order
        foreach ($orderItems as $item) {
            foreach ($shipmentItems as $shipmentItem) {
                if ($shipmentItem->getOrderItemId() == $item->getItemId()) {
                    $item->setQtyShipped($item->getQtyShipped() - $shipmentItem->getQty());
                }
            }
        }

        // Delete shipment info
        $connection->rawQuery('DELETE FROM `'.$shipmentGridTable.'` WHERE entity_id='.$shipmentId);
        $connection->rawQuery('DELETE FROM `'.$shipmentTable.'` WHERE entity_id='.$shipmentId);
        if ($order->hasShipments() || $order->hasInvoices() || $order->hasCreditmemos()) {
            $order->setState(\Magento\Sales\Model\Order::STATE_PROCESSING)
                ->setStatus($order->getConfig()->getStateDefaultStatus(\Magento\Sales\Model\Order::STATE_PROCESSING))
                ->save();
        } else {
            $order->setState(\Magento\Sales\Model\Order::STATE_NEW)
                ->setStatus($order->getConfig()->getStateDefaultStatus(\Magento\Sales\Model\Order::STATE_NEW))
                ->save();
        }

        return $order;
    }
}
