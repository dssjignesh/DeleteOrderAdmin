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
use Magento\Sales\Api\CreditmemoRepositoryInterface;
use Dss\DeleteOrder\Model\Creditmemo\Delete;

class Creditmemo extends Action
{
    /**
     * Creditmemo constructor.
     *
     * @param Action\Context $context
     * @param CreditmemoRepositoryInterface $creditmemoRepository
     * @param Delete $delete
     */
    public function __construct(
        Action\Context $context,
        protected CreditmemoRepositoryInterface $creditmemoRepository,
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
        $creditmemoId = $this->getRequest()->getParam('creditmemo_id');
        $creditmemo = $this->creditmemoRepository->get($creditmemoId);
        try {
            $this->delete->deleteCreditmemo($creditmemoId);
            $this->messageManager->addSuccessMessage(__(
                'Successfully deleted credit memo #%1.',
                $creditmemo->getIncrementId()
            ));
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(__('Error delete credit memo #%1.', $creditmemo->getIncrementId()));
        }
        $resultRedirect = $this->resultRedirectFactory->create();
        $resultRedirect->setPath('sales/creditmemo/');
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
