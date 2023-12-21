<?php

namespace Vbdev\StoreCredit\Api;

interface StoreCreditManagementInterface
{
    /**
     * Return the customer's store credit. If the customer Id is not found, it will be ignored and only those found will be returned.
     * If none of the customer Ids are found, an empty array will be returned.
     * @param string[] $customerIds
     * @return \Vbdev\StoreCredit\Api\Data\StoreCreditInterface[]
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function get(array $customerIds);

    /**
     * Create store credit for a customer.
     * If any items will have invalid customer_id, website_id or amount, they will be marked as failed and excluded from
     * create list and \Vbdev\StoreCredit\Api\Data\StoreCreditResultInterface[] with problem description will be returned.
     * If there were no failed items during update empty array will be returned.
     * If error occurred during the update exception will be thrown.
     *
     * @param \Vbdev\StoreCredit\Api\Data\StoreCreditInterface[] $storeCredits
     * @return \Vbdev\StoreCredit\Api\Data\StoreCreditResultInterface[]
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function create(array $storeCredits);

    /**
     * Update customer's store credit.
     * If any items will have invalid customer_id, website_id or amount, they will be marked as failed and excluded from
     * update list and \Vbdev\StoreCredit\Api\Data\StoreCreditResultInterface[] with problem description will be returned.
     * If there were no failed items during update empty array will be returned.
     * If error occurred during the update exception will be thrown.
     *
     * @param \Vbdev\StoreCredit\Api\Data\StoreCreditInterface[] $storeCredits
     * @return \Vbdev\StoreCredit\Api\Data\StoreCreditResultInterface[]
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function update(array $storeCredits);
}
