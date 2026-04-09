<?php
namespace VVGroup\PriceSync\Api;

interface PriceSyncInterface
{
    /**
     * Get product prices
     *
     * @param string $store_code
     * @param int $page
     * @param int $page_size
     * @return array
     */
    public function getPrices($store_code, $page = 1, $page_size = 20);
}