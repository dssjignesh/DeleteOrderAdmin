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
use Magento\Sales\Api\InvoiceRepositoryInterface;
use Dss\DeleteOrder\Model\Invoice\Delete;

class Invoice extends Action
{
    /**
     * Invoice constructor.
     *
     * @param Action\Context $context
     * @param InvoiceRepositoryInterface $invoiceRepository
     * @param Delete $delete
     */
    public function __construct(
        Action\Context $context,
        protected InvoiceRepositoryInterface $invoiceRepository,
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
        $invoiceId = $this->getRequest()->getParam('invoice_id');
        $invoice = $this->invoiceRepository->get($invoiceId);
        try {
            $this->delete->deleteInvoice($invoiceId);
            $this->messageManager->addSuccessMessage(__(
                'Successfully deleted invoice #%1.',
                $invoice->getIncrementId()
            ));
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(__('Error delete invoice #%1.', $invoice->getIncrementId()));
        }
        $resultRedirect = $this->resultRedirectFactory->create();
        $resultRedirect->setPath('sales/invoice/');
        return $resultRedirect;
    }

    /**
     * Check permission via ACL resource
     *
     * @var bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Dss_DeleteOrder::delete_order');
    }
}
