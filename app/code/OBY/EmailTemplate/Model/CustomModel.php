<?php

namespace OBY\EmailTemplate\Model;

use Magento\Framework\Model\AbstractModel;
use OBY\EmailTemplate\Model\ResourceModel\CustomData\CollectionFactory as CustomDataCollectionFactory;

class CustomModel extends AbstractModel
{
    private $customDataCollectionFactory;

    public function __construct(
        CustomDataCollectionFactory $customDataCollectionFactory
    ) {
        $this->customDataCollectionFactory = $customDataCollectionFactory;
    }

    public function getCustomData()
    {
        $customDataCollection = $this->customDataCollectionFactory->create();
        // Apply necessary filters or conditions to fetch the desired data
        $customDataCollection->addFieldToFilter('status', 1);

        return $customDataCollection->getItems();
    }
}
