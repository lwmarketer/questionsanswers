<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Lovevox\QuestionsAnswers\Controller\Adminhtml\Questions;

use Lovevox\QuestionsAnswers\Controller\Adminhtml\Question as ProductController;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\Action\HttpPostActionInterface;

/**
 * Pending reviews grid.
 */
class Index extends ProductController implements HttpGetActionInterface, HttpPostActionInterface
{
    /**
     * Execute action.
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        if ($this->getRequest()->getParam('ajax')) {
            /** @var \Magento\Backend\Model\View\Result\Forward $resultForward */
            $resultForward = $this->resultFactory->create(ResultFactory::TYPE_FORWARD);
            $resultForward->forward('IndexGrid');
            return $resultForward;
        }
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $resultPage->setActiveMenu('Lovevox_QuestionsAnswers::product_questionsanswers_all');
//        $resultPage->getConfig()->getTitle()->prepend(__('Customer Reviews'));
        $resultPage->getConfig()->getTitle()->prepend(__('Product Q&amp;A'));
        $resultPage->addContent($resultPage->getLayout()->createBlock(\Lovevox\QuestionsAnswers\Block\Adminhtml\Main::class));
        return $resultPage;
    }

    /**
     * @inheritdoc
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Lovevox_QuestionsAnswers::product_questionsanswers_all');
    }
}
