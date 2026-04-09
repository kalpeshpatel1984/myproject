<?php
namespace VVGroup\PriceSync\Model;

use VVGroup\PriceSync\Api\PriceSyncInterface;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Exception\LocalizedException;

class PriceSync implements PriceSyncInterface
{
    protected $collectionFactory;
    protected $storeManager;

    public function __construct(
        CollectionFactory $collectionFactory,
        StoreManagerInterface $storeManager
    ) {
        $this->collectionFactory = $collectionFactory;
        $this->storeManager = $storeManager;
    }

    public function getPrices($store_code, $page = 1, $page_size = 20)
    {
        try {
            if (!$store_code) {
                throw new LocalizedException(__('store_code is required'));
            }

            if ($page_size > 100) {
                $page_size = 100;
            }

            // Get store
            $store = $this->storeManager->getStore($store_code);

            // Product Collection
            $collection = $this->collectionFactory->create();
            $collection->setStore($store);
            $collection->addAttributeToSelect(['sku', 'price', 'special_price']);
            $collection->setPageSize($page_size);
            $collection->setCurPage($page);

            $items = [];

            foreach ($collection as $product) {
                $items[] = [
                    'sku' => $product->getSku(),
                    'price' => (float)$product->getPrice(),
                    'special_price' => $product->getSpecialPrice() ? (float)$product->getSpecialPrice() : null
                ];
            }

            return [
                'store_code' => $store_code,
                'page' => (int)$page,
                'total_count' => (int)$collection->getSize(),
                'items' => $items
            ];

        } catch (\Exception $e) {
            throw new LocalizedException(__($e->getMessage()));
        }
    }
}