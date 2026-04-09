<?php

namespace VVGroup\StoreBadge\Block;

use Magento\Framework\View\Element\Template;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Registry;
use Magento\Catalog\Api\ProductRepositoryInterface;

class StoreBadge extends Template
{
    protected $storeManager;
    protected $registry;
    protected $productRepository;

    public function __construct(
        Template\Context $context,
        StoreManagerInterface $storeManager,
        Registry $registry,
        ProductRepositoryInterface $productRepository,
        array $data = []
    ) {
        $this->storeManager = $storeManager;
        $this->registry = $registry;
        $this->productRepository = $productRepository;
        parent::__construct($context, $data);
    }

    /**
     * Get current product
     */
    public function getCurrentProduct()
    {
        return $this->registry->registry('current_product');
    }

    /**
     * Get store names where SKU exists
     */
    public function getStoreNames(): array
    {
        $product = $this->getCurrentProduct();

        if (!$product || !$product->getSku()) {
            return [];
        }

        $sku = $product->getSku();
        $stores = $this->storeManager->getStores();
        $storeNames = [];

        foreach ($stores as $store) {
            try {
                $storeProduct = $this->productRepository->get(
                    $sku,
                    false,
                    $store->getId()
                );

                if ($storeProduct->getId() && $store->getIsActive()) {
                    $storeNames[] = $store->getName();
                }
            } catch (\Exception $e) {
                continue;
            }
        }

        return array_unique($storeNames);
    }
}