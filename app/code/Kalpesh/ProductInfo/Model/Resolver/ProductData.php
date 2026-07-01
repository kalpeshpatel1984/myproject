<?php

namespace Kalpesh\ProductInfo\Model\Resolver;

use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Store\Model\StoreManagerInterface;

class ProductData implements ResolverInterface
{
    protected $productCollectionFactory;
    protected $storeManager;

    public function __construct(
        CollectionFactory $productCollectionFactory,
        StoreManagerInterface $storeManager
    ) {
        $this->productCollectionFactory = $productCollectionFactory;
        $this->storeManager = $storeManager;
    }

    public function resolve(
        Field $field,
        $context,
        ResolveInfo $info,
        array $value = null,
        array $args = null
    ) {
        $collection = $this->productCollectionFactory->create();
        $collection->addAttributeToSelect(['name', 'sku', 'price', 'description', 'image']);
        $collection->setPageSize(10); // limit products (optional)

        $products = [];

        foreach ($collection as $product) {

            $imageUrl = $this->storeManager->getStore()->getBaseUrl() 
                . 'media/catalog/product' 
                . $product->getImage();

            $products[] = [
                'id' => $product->getId(),
                'name' => $product->getName(),
                'sku' => $product->getSku(),
                'price' => $product->getPrice(),
                'description' => $product->getDescription(),
                'image' => $imageUrl
            ];
        }

        return $products;
    }
}