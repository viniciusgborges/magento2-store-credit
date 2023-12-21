<?php

namespace Vbdev\StoreCredit\Api;

/**
 * Store Credit resource model.
 * @api
 */
interface StoreCreditInterface
{
    /**
     * Get customer store credit by customerIds.
     *
     * @param string[] $customerIds Array containing customerIds
     *     $customerIds = [
     *         'customerId value 1',
     *         'customerId value 2'
     *     ];
     * @return [
     *      'website_id' => (int) Website Id.
     *      'amount' => (double) amount of store credit.
     * ]
     */
    public function get(array $customerIds);

    /**
     * Create store credit.
     *
     * @param array $storeCredits
     *      $storeCredits = [
     *          'customer_id' => (int) Customer Id. Required.
     *          'website_id' => (int) Website Id. Required.
     *          'amount' => (double) amount. Required.
     *      ];
     * @return bool
     * @throws \Magento\Framework\Exception\CouldNotSaveException Thrown if error occurred during store credit create.
     */
    public function create(array $storeCredits);

    /**
     * Update store credits.
     *
     * @param array $storeCredits
     *      $storeCredits = [
     *           'customer_id' => (int) Customer Id. Required.
     *           'website_id' => (int) Website Id. Required.
     *           'amount' => (double) amount. Required.
     *       ];
     * @return bool
     * @throws \Magento\Framework\Exception\CouldNotSaveException Thrown if error occurred during store credit update.
     */
    public function update(array $storeCredits);
}
