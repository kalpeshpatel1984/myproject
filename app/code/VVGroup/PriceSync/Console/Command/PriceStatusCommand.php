<?php

namespace VVGroup\PriceSync\Console\Command;

use Magento\Framework\App\State;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\Table;

class PriceStatusCommand extends Command
{
    const COMMAND_NAME = 'vvgroup:pricesync:status';

    protected $appState;
    protected $storeManager;
    protected $productCollectionFactory;

    public function __construct(
        State $appState,
        StoreManagerInterface $storeManager,
        CollectionFactory $productCollectionFactory
    ) {
        $this->appState = $appState;
        $this->storeManager = $storeManager;
        $this->productCollectionFactory = $productCollectionFactory;

        parent::__construct();
    }

    protected function configure()
    {
        $this->setName(self::COMMAND_NAME)
            ->setDescription('Check product count per store and display status (OK / EMPTY)');

        parent::configure();
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $this->appState->setAreaCode('adminhtml');
        } catch (\Exception $e) {
            // already set
        }

        $stores = $this->storeManager->getStores();

        $table = new Table($output);
        $table->setHeaders([
            'Store ID',
            'Store Code',
            'Store Name',
            'Product Count',
            'Status'
        ]);

        foreach ($stores as $store) {

            $collection = $this->productCollectionFactory->create();
            $collection->setStoreId($store->getId());
            $collection->addAttributeToSelect('entity_id');
            $collection->addStoreFilter($store->getId());

            $count = $collection->getSize();

            // Status logic
            if ($count == 0) {
                $status = '<fg=red>EMPTY</>';
            } else {
                $status = '<fg=green>OK</>';
            }

            $table->addRow([
                $store->getId(),
                $store->getCode(),
                $store->getName(),
                $count,
                $status
            ]);
        }

        $output->writeln('<info>Store-wise Product Status</info>');
        $table->render();

        return Command::SUCCESS;
    }
}