<?php

namespace Vbdev\StoreCredit\Model\Customer\Validation;

use Vbdev\StoreCredit\Api\Data\StoreCreditResultInterface;
use Vbdev\StoreCredit\Api\Data\StoreCreditResultInterfaceFactory;

/**
 * Validation Result is used to aggregate errors that occurred during store credit update.
 *
 * @api
 */
class Result
{
    /**
     * Failed items.
     *
     * @var array
     */
    private $failedItems = [];

    /**
     * @param StoreCreditResultInterfaceFactory $creditResultInterfaceFactory
     */
    public function __construct(
        protected StoreCreditResultInterfaceFactory $creditResultInterfaceFactory,
    ) {
    }

    /**
     * Add failed store credit identified, message and message parameters, that occurred during store credit update.
     *
     * @param int $id Failed store credit identified.
     * @param string $message Failure reason message.
     * @param array $parameters (optional). Placeholder values in ['placeholder key' => 'placeholder value'] format
     * for failure reason message.
     * @return void
     */
    public function addFailedItem($id, $message, array $parameters = [])
    {
        $this->failedItems[$id][] = [
            'message' => $message,
            'parameters' => $parameters
        ];
    }

    /**
     * Get ids of rows, that contained errors during store credit update.
     *
     * @return int[]
     */
    public function getFailedRowIds()
    {
        return $this->failedItems ? array_keys($this->failedItems) : [];
    }

    /**
     * Get store credit update errors, that occurred during store credit update.
     *
     * @return StoreCreditResultInterface[]
     */
    public function getFailedItems()
    {
        $failedItems = [];

        foreach ($this->failedItems as $items) {
            foreach ($items as $failedRecord) {
                $resultItem = $this->creditResultInterfaceFactory->create();
                $resultItem->setMessage($failedRecord['message']);
                $resultItem->setParameters($failedRecord['parameters']);
                $failedItems[] = $resultItem;
            }
        }

        /**
         * Clear validation messages to prevent wrong validation for subsequent store credit update.
         * Work around for backward compatible changes.
         */
        $this->failedItems = [];

        return $failedItems;
    }
}
