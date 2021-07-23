<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Lovevox\QuestionsAnswers\Block\Adminhtml;

use Lovevox\QuestionsAnswers\Model\ResourceModel\QuestionsAnswers\CatalogProductQuestionCollection;

/**
 * Class Grid
 * @method \Lovevox\QuestionsAnswers\Block\Adminhtml\Grid setMassactionIdFieldOnlyIndexValue()
 * setMassactionIdFieldOnlyIndexValue(bool $onlyIndex)
 * @package Lovevox\QuestionsAnswers\Block\Adminhtml
 */
class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{
    /**
     * Review action pager
     *
     * @var \Lovevox\QuestionsAnswers\Helper\Action\Pager
     */
    protected $_questionActionPager = null;

    /**
     * Review data
     *
     * @var \Lovevox\QuestionsAnswers\Helper\Data
     */
    protected $_questionData = null;

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;


    /**
     * Review model factory
     *
     * @var \Lovevox\QuestionsAnswers\Model\CatalogProductQuestion
     */
    protected $_catalogProductQuestionFactory;

    /**
     * @var \Lovevox\QuestionsAnswers\Model\ResourceModel\QuestionsAnswers\CatalogProductQuestionCollectionFactory
     */
    protected $_catalogProductQuestionCollectionFactory;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\Collection
     */
    protected $_productCollectionFactory;

    /**
     * Grid constructor.
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Lovevox\QuestionsAnswers\Model\CatalogProductQuestionFactory $catalogProductQuestionFactory
     * @param \Lovevox\QuestionsAnswers\Model\ResourceModel\QuestionsAnswers\CatalogProductQuestionCollectionFactory $catalogProductQuestionCollectionFactory
     * @param \Lovevox\QuestionsAnswers\Helper\Data $questionData
     * @param \Lovevox\QuestionsAnswers\Helper\Action\Pager $questionActionPager
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Lovevox\QuestionsAnswers\Model\CatalogProductQuestionFactory $catalogProductQuestionFactory,
        \Lovevox\QuestionsAnswers\Model\ResourceModel\QuestionsAnswers\CatalogProductQuestionCollectionFactory $catalogProductQuestionCollectionFactory,
        \Lovevox\QuestionsAnswers\Helper\Data $questionData,
        \Lovevox\QuestionsAnswers\Helper\Action\Pager $questionActionPager,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        array $data = []
    )
    {
        $this->_coreRegistry = $coreRegistry;
        $this->_questionData = $questionData;
        $this->_questionActionPager = $questionActionPager;
        $this->_catalogProductQuestionFactory = $catalogProductQuestionFactory;
        $this->_catalogProductQuestionCollectionFactory = $catalogProductQuestionCollectionFactory;
        $this->_productCollectionFactory = $productCollectionFactory->create()->addFieldToSelect('name');
        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * Initialize grid
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('indexGrid');
        $this->setDefaultSort('created_at');
    }

    /**
     * Save search results
     *
     * @return \Magento\Backend\Block\Widget\Grid
     */
    protected function _afterLoadCollection()
    {
        /** @var $actionPager \Lovevox\QuestionsAnswers\Helper\Action\Pager */
        $actionPager = $this->_questionActionPager;
        $actionPager->setStorageId('question_id');
        $actionPager->setItems($this->getCollection()->getResultingIds());
        $collection = $this->getCollection();
//        var_dump($collection->count());
        foreach ($collection as $item) {
            $product_id = $item->getData('product_id');
            /** @var \Magento\Catalog\Model\Product $product */
            $product = $this->_productCollectionFactory->getItemById($product_id);
            if($product){
                $item->setData('product_name', $product->getName());
                $item->setData('sku', $product->getSku());
                $item->setData('url', $product->getProductUrl());
            }else{
                $item->setData('product_name', "");
                $item->setData('sku', "");
                $item->setData('url', "");
            }
        }
        $this->setCollection($collection);
        return parent::_afterLoadCollection();
    }

    /**
     * Prepare collection
     *
     * @return \Magento\Review\Block\Adminhtml\Grid
     */
    protected function _prepareCollection()
    {
        /** @var $model \Lovevox\QuestionsAnswers\Model\CatalogProductQuestion */
        $model = $this->_catalogProductQuestionFactory->create();
        /** @var $collection \Lovevox\QuestionsAnswers\Model\ResourceModel\QuestionsAnswers\CatalogProductQuestionCollection */
        $collection = $this->_catalogProductQuestionCollectionFactory->create();

        if ($this->getEntityId() || $this->getRequest()->getParam('entityId', false)) {
            $entityId = $this->getEntityId();
            if (!$entityId) {
                $entityId = $this->getRequest()->getParam('entityId');
            }
            $this->setEntityId($entityId);
            $collection->addEntityFilter($this->getEntityId());
        }

        if ($this->getCustomerId() || $this->getRequest()->getParam('customerId', false)) {
            $customerId = $this->getCustomerId();
            if (!$customerId) {
                $customerId = $this->getRequest()->getParam('customerId');
            }
            $this->setCustomerId($customerId);
            $collection->addCustomerFilter($this->getCustomerId());
        }

        if ($this->_coreRegistry->registry('usePendingFilter') === true) {
            $collection->addStatusFilter($model->getPendingStatus());
        }

        $collection->addStoreData();

        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * Prepare grid columns
     *
     * @return \Magento\Backend\Block\Widget\Grid
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            'entity_id',
            [
                'header' => __('ID'),
                'filter_index' => 'entity_id',
                'index' => 'entity_id',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id'
            ]
        );

        $this->addColumn(
            'sku',
            [
                'header' => __('SKU'),
                'filter_index' => 'sku',
                'index' => 'sku',
                'type' => 'text',
                'truncate' => 50,
                'escape' => true
            ]
        );

        $this->addColumn(
            'product_name',
            [
                'header' => __('Product Name'),
                'filter_index' => 'product_name',
                'index' => 'product_name',
                'type' => 'text',
                'truncate' => 50,
                'escape' => true
            ]
        );

        $this->addColumn(
            'url',
            [
                'header' => __('URL'),
                'filter_index' => 'url',
                'index' => 'url',
                'type' => 'text',
                'truncate' => 255,
                'escape' => true
            ]
        );

        $this->addColumn(
            'title',
            [
                'header' => __('Question'),
                'filter_index' => 'title',
                'index' => 'title',
                'type' => 'text',
                'truncate' => 255,
                'escape' => true
            ]
        );

        $this->addColumn(
            'name',
            [
                'header' => __('Customer Name'),
                'filter_index' => 'name',
                'index' => 'name',
                'type' => 'text',
                'truncate' => 255,
                'escape' => true
            ]
        );

        $this->addColumn(
            'email',
            [
                'header' => __('Email'),
                'filter_index' => 'email',
                'index' => 'email',
                'type' => 'text',
                'truncate' => 255,
                'escape' => true
            ]
        );

        $this->addColumn(
            'is_show',
            [
                'header' => __('Is Private'),
                'type' => 'options',
                'options' => [
                    1 => __('Yes'),
                    0 => __('No')
                ],
                'filter_index' => 'is_show',
                'index' => 'is_show'
            ]
        );

        if (!$this->_coreRegistry->registry('usePendingFilter')) {
            $this->addColumn(
                'status',
                [
                    'header' => __('Status'),
                    'type' => 'options',
                    'options' => $this->_questionData->getQuestionStatuses(),
                    'filter_index' => 'status',
                    'index' => 'status'
                ]
            );
        }

        $this->addColumn(
            'created_at',
            [
                'header' => __('Created'),
                'type' => 'datetime',
                'filter_index' => 'created_at',
                'index' => 'created_at',
                'header_css_class' => 'col-date col-date-min-width',
                'column_css_class' => 'col-date'
            ]
        );

        $this->addColumn(
            'updated_at',
            [
                'header' => __('Modified'),
                'type' => 'datetime',
                'filter_index' => 'updated_at',
                'index' => 'updated_at',
                'header_css_class' => 'col-date col-date-min-width',
                'column_css_class' => 'col-date'
            ]
        );


        $this->addColumn(
            'action',
            [
                'header' => __('Action'),
                'type' => 'action',
                'getter' => 'getEntityId',
                'actions' => [
                    [
                        'caption' => __('Edit'),
                        'url' => [
                            'base' => 'questionsanswers/questions/edit',
                            'params' => [
                                'id' => $this->getEntityId(),
                                'ret' => $this->_coreRegistry->registry('usePendingFilter') ? 'pending' : null,
                            ],
                        ],
                        'field' => 'id',
                    ],
                ],
                'filter' => false,
                'sortable' => false
            ]
        );

        $block = $this->getLayout()->getBlock('grid.bottom.links');
        if ($block) {
            $this->setChild('grid.bottom.links', $block);
        }

        return parent::_prepareColumns();
    }

    /**
     * Prepare grid mass actions
     *
     * @return void
     */
    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('entity_id');
        $this->setMassactionIdFilter('entity_id');
        $this->setMassactionIdFieldOnlyIndexValue(true);
        $this->getMassactionBlock()->setFormFieldName('question_id');

        $this->getMassactionBlock()->addItem(
            'delete',
            [
                'label' => __('Delete'),
                'url' => $this->getUrl(
                    '*/*/massDelete',
                    ['ret' => $this->_coreRegistry->registry('usePendingFilter') ? 'pending' : 'index']
                ),
                'confirm' => __('Are you sure?')
            ]
        );

        $statuses = $this->_questionData->getQuestionStatusesOptionArray();
        array_unshift($statuses, ['label' => '', 'value' => '']);
        $this->getMassactionBlock()->addItem(
            'update_status',
            [
                'label' => __('Update Status'),
                'url' => $this->getUrl(
                    '*/*/massUpdateStatus',
                    ['ret' => $this->_coreRegistry->registry('usePendingFilter') ? 'pending' : 'index']
                ),
                'additional' => [
                    'status' => [
                        'name' => 'status',
                        'type' => 'select',
                        'class' => 'required-entry',
                        'label' => __('Status'),
                        'values' => $statuses,
                    ],
                ]
            ]
        );
    }

    /**
     * @inheritdoc
     */
    protected function _prepareMassactionColumn()
    {
        parent::_prepareMassactionColumn();
        /** needs for correct work of mass action select functionality */
        $this->setMassactionIdField('entity_id');

        return $this;
    }

    /**
     * Get row url
     *
     * @param \Magento\Review\Model\Review|\Magento\Framework\DataObject $row
     * @return string
     */
    public function getRowUrl($row)
    {
        return $this->getUrl(
            'questionsanswers/questions/edit',
            [
                'id' => $row->getEntityId(),
                'ret' => $this->_coreRegistry->registry('usePendingFilter') ? 'pending' : null
            ]
        );
    }

    /**
     * Get grid url
     *
     * @return string
     */
    public function getGridUrl()
    {
        if ($this->getEntityId()) {
            return $this->getUrl(
                'questionsanswers/questions' . ($this->_coreRegistry->registry('usePendingFilter') ? 'pending' : ''),
                ['entityId' => $this->getEntityId()]
            );
        } else {
            return $this->getCurrentUrl();
        }
    }
}
