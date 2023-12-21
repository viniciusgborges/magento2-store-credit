<?php

namespace Vbdev\StoreCredit\Model\Customer;

use Magento\Framework\Model\AbstractExtensibleModel;
use Vbdev\StoreCredit\Api\Data\StoreCreditResultInterface;

/**
 * {@inheritdoc}
 */
class StoreCreditResult extends AbstractExtensibleModel implements StoreCreditResultInterface
{
    /**
     * @inheritDoc
     */
    public function getMessage()
    {
        return $this->getData(self::MESSAGE);
    }

    /**
     * @inheritDoc
     */
    public function setMessage($message)
    {
        return $this->setData(self::MESSAGE, $message);
    }

    /**
     * @inheritDoc
     */
    public function getParameters()
    {
        return $this->getData(self::PARAMETERS);
    }

    /**
     * @inheritDoc
     */
    public function setParameters(array $parameters)
    {
        return $this->setData(self::PARAMETERS, $parameters);
    }
}
