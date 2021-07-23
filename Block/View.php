<?php

namespace Lovevox\QuestionsAnswers\Block;

use Magento\Framework\Data\Collection;
use Magento\Framework\View\Element\Template\Context;
use Magento\Reports\Block\Product\Viewed;
use Magento\Catalog\Model\ProductFactory;

class View extends \Magento\Framework\View\Element\Template
{

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;


    protected $catalogProductQuestionCollectionFactory;

    /**
     * View constructor.
     * @param Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Lovevox\QuestionsAnswers\Model\ResourceModel\QuestionsAnswers\CatalogProductQuestionCollectionFactory $catalogProductQuestionsCollectionFactory
     * @param array $data
     */
    public function __construct(
        Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Lovevox\QuestionsAnswers\Model\ResourceModel\QuestionsAnswers\CatalogProductQuestionCollectionFactory $catalogProductQuestionCollectionFactory,
        array $data = []
    )
    {
        $this->_coreRegistry = $coreRegistry;
        $this->catalogProductQuestionCollectionFactory = $catalogProductQuestionCollectionFactory;
        parent::__construct($context, $data);
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
    }


    /**
     * @return mixed
     */
    public function getProduct()
    {
        if (!$this->hasData('product')) {
            $this->setData('product', $this->_coreRegistry->registry('product'));
        }
        return $this->getData('product');
    }

    /**
     * @param $product_id
     * @return \Lovevox\QuestionsAnswers\Model\ResourceModel\QuestionsAnswers\CatalogProductQuestionCollection
     */
    public function getQuestionList($product_id)
    {
        $collection = $this->catalogProductQuestionCollectionFactory->create();
        $collection->getSelect()
            ->joinLeft(['answer' => 'catalog_product_answer_entity'],
                'main_table.entity_id=answer.question_id',['reply_name'=>'answer.name','reply_content'=>'answer.content','reply_date'=>'answer.created_at'])
            ->where('main_table.is_show = 0')
            ->where('main_table.status = 3')
            ->where('main_table.product_id ='. $product_id)
            ->order('main_table.entity_id', Collection::SORT_ORDER_DESC);
        return $collection;
    }
}

