<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Lovevox\QuestionsAnswers\Controller\Adminhtml\Questions;

use Magento\Backend\App\Action\Context;
use Magento\Framework\Registry;
use Lovevox\QuestionsAnswers\Controller\Adminhtml\Question as ProductController;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\App\Action\HttpPostActionInterface;

/**
 * Mass Update Status action.
 */
class MassUpdateStatus extends ProductController implements HttpPostActionInterface
{



    /**
     * @var \Lovevox\QuestionsAnswers\Model\ResourceModel\QuestionsAnswers\CatalogProductQuestionCollection
     */
    private $collection;

    /**
     * @var \Lovevox\QuestionsAnswers\Model\ResourceModel\QuestionsAnswers\CatalogProductQuestionCollectionFactory
     */
    private $collectionFactory;

    /**
     * @param Context $context
     * @param Registry $coreRegistry
     * @param \Lovevox\QuestionsAnswers\Model\CatalogProductQuestionFactory $reviewFactory
     * @param \Lovevox\QuestionsAnswers\Model\ResourceModel\QuestionsAnswers\CatalogProductQuestionCollectionFactory $collectionFactory
     */
    public function __construct(
        Context $context,
        Registry $coreRegistry,
        \Lovevox\QuestionsAnswers\Model\CatalogProductQuestionFactory $catalogProductQuestionFactory,
        \Lovevox\QuestionsAnswers\Model\ResourceModel\QuestionsAnswers\CatalogProductQuestionCollectionFactory $collectionFactory
    ) {
        parent::__construct($context, $coreRegistry,$catalogProductQuestionFactory);
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * Execute action.
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        $questionIds = $this->getRequest()->getParam('question_id');
        if (!is_array($questionIds)) {
            $this->messageManager->addErrorMessage(__('Please select question(s).'));
        } else {
            try {
                $status = $this->getRequest()->getParam('status');
                foreach ($this->getCollection() as $model) {
                    $model->setStatus($status)->save()->aggregate();
                }
                $this->messageManager->addSuccessMessage(
                    __('A total of %1 record(s) have been updated.', count($questionIds))
                );
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addExceptionMessage(
                    $e,
                    __('Something went wrong while updating these question(s).')
                );
            }
        }
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $resultRedirect->setPath('questionsanswers/*/' . $this->getRequest()->getParam('ret', 'index'));
        return $resultRedirect;
    }

    /**
     * @inheritdoc
     */
    protected function _isAllowed()
    {
        if (parent::_isAllowed()) {
            return true;
        }

        if (!$this->_authorization->isAllowed('Magento_Review::pending')) {
            return false;
        }

        foreach ($this->getCollection() as $model) {
            if ($model->getStatusId() != Review::STATUS_PENDING) {
                $this->messageManager->addErrorMessage(
                    __(
                        'You don’t have permission to perform this operation. '
                        . 'Selected questions must be in Pending Status only.'
                    )
                );

                return false;
            }
        }

        return true;
    }

    /**
     * Returns requested collection.
     *
     * @return \Lovevox\QuestionsAnswers\Model\ResourceModel\QuestionsAnswers\CatalogProductQuestionCollection
     */
    private function getCollection(): \Lovevox\QuestionsAnswers\Model\ResourceModel\QuestionsAnswers\CatalogProductQuestionCollection
    {
        if ($this->collection === null) {
            $collection = $this->collectionFactory->create();
            $collection->addFieldToFilter(
                'main_table.' . $collection->getResource()
                    ->getIdFieldName(),
                $this->getRequest()->getParam('question_id')
            );

            $this->collection = $collection;
        }

        return $this->collection;
    }
}
