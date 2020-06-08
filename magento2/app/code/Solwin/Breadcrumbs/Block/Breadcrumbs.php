<?php
namespace Solwin\Breadcrumbs\Block;

use Magento\Catalog\Helper\Data;
use Magento\Framework\View\Element\Template\Context;
use Magento\Store\Model\Store;
use Magento\Framework\Registry;

class Breadcrumbs extends \Magento\Framework\View\Element\Template
{
    /**
     * Catalog data
     *
     * @var Data
     */
    protected $_catalogData = null;
    /**
     * @param Context $context
     * @param Data $catalogData
     * @param array $data
     */
    public function __construct(
        Context $context,
        Data $catalogData,
        Registry $registry,
        array $data = []
    ) {
        $this->_catalogData = $catalogData;
        $this->registry = $registry;
        parent::__construct($context, $data);
    }
    public function getCrumbs()
    {
        $breadcrumbs = [];

        $breadcrumbs[] = [
            'label' => 'Home',
            'title' => 'Go to Home Page',
            'link' => $this->_storeManager->getStore()->getBaseUrl()
        ];
        $path = $this->_catalogData->getBreadcrumbPath();
        $product = $this->registry->registry('current_product');
        $categoryCollection = clone $product->getCategoryCollection();
        $categoryCollection->clear();
        $categoryCollection
          ->addAttributeToSort('level', $categoryCollection::SORT_ORDER_DESC)
          ->addAttributeToFilter(
              'path',
              ['like' => "1/" . $this->_storeManager->getStore()->getRootCategoryId() . "/%"]
          );
        $categoryCollection->setPageSize(1);
        $breadcrumbCategories = $categoryCollection->getFirstItem()->getParentCategories();
        foreach ($breadcrumbCategories as $category) {
            $breadcrumbs[] = [
                'label' => $category->getName(),
                'title' => $category->getName(),
                'link' => $category->getUrl()
            ];
        }

        $breadcrumbs[] = [
                'label' => $product->getName(),
                'title' => $product->getName(),
                'link' => ''
            ];

        return $breadcrumbs;
    }
}
