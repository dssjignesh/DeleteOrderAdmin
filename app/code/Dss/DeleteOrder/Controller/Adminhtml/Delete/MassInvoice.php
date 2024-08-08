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
use Magento\Sales\Model\ResourceModel\Order\Invoice\CollectionFactory as InvoiceCollectionFactory;
use Magento\Sales\Api\InvoiceRepositoryInterface;
use Dss\DeleteOrder\Model\Invoice\Delete;
use Magento\Sales\Controller\Adminhtml\Order\AbstractMassAction;

class MassInvoice extends AbstractMassAction
{
    /**
     * MassInvoice Constructor
     *
     * @param Context $context
     * @param Filter $filter
     * @param CollectionFactory $collectionFactory
     * @param CollectionFactory $invoiceCollectionFactory
     * @param InvoiceRepositoryInterface $invoiceRepository
     * @param Delete $delete
     */
    public function __construct(
        Context $context,
        Filter $filter,
        CollectionFactory $collectionFactory,
        protected InvoiceCollectionFactory $invoiceCollectionFactory,
        protected InvoiceRepositoryInterface $invoiceRepository,
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
        $invoiceCollection = $this->invoiceCollectionFactory->create();
        $collectionInvoice = $this->filter->getCollection($invoiceCollection);
        foreach ($collectionInvoice as $invoice) {
            array_push($selected, $invoice->getId());
        }
        if ($selected) {
            foreach ($selected as $invoiceId) {
                $invoice = $this->invoiceRepository->get($invoiceId);
                try {
                    $order = $this->delete->deleteInvoice($invoiceId);
                    $this->messageManager->addSuccessMessage(
                        __(
                            'Successfully deleted invoice #%1.',
                            $invoice->getIncrementId()
                        )
                    );
                } catch (\Exception $e) {
                    $this->messageManager->addErrorMessage(__('Error delete invoice #%1.', $invoice->getIncrementId()));
                }
            }
        }

        $resultRedirect = $this->resultRedirectFactory->create();
        if ($params['namespace'] == 'sales_order_view_invoice_grid' && isset($order)) {
            $resultRedirect->setPath('sales/order/view', ['order_id' => $order->getId()]);
        } else {
            $resultRedirect->setPath('sales/invoice/');
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
