<?php

namespace Vbdev\StoreCredit\Model\Customer;

use Magento\Framework\Model\AbstractExtensibleModel;
use Vbdev\StoreCredit\Api\Data\StoreCreditInterface;

/**
 * Customer Store Credit class is used to encapsulate data that can be processed by efficient store credit API.
 */
class StoreCredit extends AbstractExtensibleModel implements StoreCreditInterface
{
    /**
     * {@inheritdoc}
     */
    public function setCustomerId($customerId)
    {
        return $this->setData(self::CUSTOMER_ID, $customerId);
    }

    /**
     * {@inheritdoc}
     */
    public function getCustomerId()
    {
        return $this->getData(self::CUSTOMER_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setWebsiteId($websiteId)
    {
        return $this->setData(self::WEBSITE_ID, $websiteId);
    }

    /**
     * {@inheritdoc}
     */
    public function getWebsiteId()
    {
        return $this->getData(self::WEBSITE_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setAmount($amount)
    {
        return $this->setData(self::AMOUNT, $amount);
    }

    /**
     * {@inheritdoc}
     */
    public function getAmount()
    {
        return $this->getData(self::AMOUNT);
    }
}
