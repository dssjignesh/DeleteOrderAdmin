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
namespace Dss\DeleteOrder\Model\Order;

use Magento\Framework\App\ResourceConnection;
use Dss\DeleteOrder\Helper\Data;
use Magento\Sales\Model\Order;

class Delete
{
    /**
     * Delete constructor.
     *
     * @param ResourceConnection $resource
     * @param \Dss\DeleteOrder\Helper\Data $data
     * @param \Magento\Sales\Model\Order $order
     */
    public function __construct(
        protected ResourceConnection $resource,
        protected Data $data,
        protected Order $order
    ) {
    }

    /**
     * Delete the odrer by Id and revert the odrer state.
     *
     * @param string $orderId
     * @throws \Exception
     */
    public function deleteOrder(string $orderId)
    {
        $connection = $this->resource->getConnection(ResourceConnection::DEFAULT_CONNECTION);

        $invoiceGridTable = $connection->getTableName($this->data->getTableName('sales_invoice_grid'));
        $shippmentGridTable = $connection->getTableName($this->data->getTableName('sales_shipment_grid'));
        $creditmemoGridTable = $connection->getTableName($this->data->getTableName('sales_creditmemo_grid'));

        $order = $this->order->load($orderId);
        $order->delete();
        $connection->rawQuery('DELETE FROM `'.$invoiceGridTable.'` WHERE order_id='.$orderId);
        $connection->rawQuery('DELETE FROM `'.$shippmentGridTable.'` WHERE order_id='.$orderId);
        $connection->rawQuery('DELETE FROM `'.$creditmemoGridTable.'` WHERE order_id='.$orderId);
    }
}
