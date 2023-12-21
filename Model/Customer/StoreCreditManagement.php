<?php

namespace Vbdev\StoreCredit\Model\Customer;

use Exception;
use Vbdev\StoreCredit\Model\Customer\Validation\Result;
use Magento\Customer\Model\ResourceModel\Customer;
use Magento\Store\Api\WebsiteRepositoryInterface;
use Vbdev\StoreCredit\Api\Data\StoreCreditInterface;
use Vbdev\StoreCredit\Api\Data\StoreCreditInterfaceFactory;
use Vbdev\StoreCredit\Api\StoreCreditInterface as StoreCreditInterfaceResource;
use Vbdev\StoreCredit\Api\StoreCreditManagementInterface;

class StoreCreditManagement implements StoreCreditManagementInterface
{
    /**
     * @param StoreCreditInterfaceResource $storeCreditResource
     * @param StoreCreditInterfaceFactory $creditInterfaceFactory
     * @param Customer $customer
     * @param WebsiteRepositoryInterface $websiteRepository
     * @param Result $validationResult
     */
    public function __construct(
        protected StoreCreditInterfaceResource $storeCreditResource,
        protected StoreCreditInterfaceFactory  $creditInterfaceFactory,
        protected Customer                     $customer,
        protected WebsiteRepositoryInterface   $websiteRepository,
        protected Result                       $validationResult
    ) {
    }

    /**
     * @inheritdoc
     */
    public function get(array $customerIds)
    {
        $rawStoreCredits = $this->storeCreditResource->get($customerIds);

        $storeCredits = [];
        foreach ($rawStoreCredits as $rawStoreCredit) {
            /** @var StoreCreditInterface $storeCredit */
            $storeCredit = $this->creditInterfaceFactory->create();
            $customerId = $rawStoreCredit['customer_id'];
            $storeCredit->setCustomerId($customerId);
            $storeCredit->setWebsiteId($rawStoreCredit['website_id']);
            $storeCredit->setAmount($rawStoreCredit['amount']);
            $storeCredits[] = $storeCredit;
        }

        return $storeCredits;
    }

    /**
     * @inheritdoc
     */
    public function create(array $storeCredits)
    {
        $storeCredits = $this->retrieveValidStoreCredits(storeCredits: $storeCredits);
        $this->storeCreditResource->create($storeCredits);

        return $this->validationResult->getFailedItems();
    }

    /**
     * @inheritdoc
     */
    public function update(array $storeCredits)
    {
        $storeCredits = $this->retrieveValidStoreCredits(storeCredits: $storeCredits);
        $this->storeCreditResource->update($storeCredits);

        return $this->validationResult->getFailedItems();
    }

    /**
     * Retrieve store credits with correct values.
     *
     * @param array $storeCredits
     * @return array
     */
    private function retrieveValidStoreCredits(array $storeCredits): array
    {
        $failedCustomerIds = array_unique(
            array_map(function ($storeCredits) {
                $customerId = $storeCredits->getCustomerId();
                if (!$this->customer->checkCustomerId($customerId)) {
                    return $customerId;
                }
                return null;
            }, $storeCredits)
        );

        foreach ($storeCredits as $key => $storeCredit) {
            if (!$storeCredit->getCustomerId() || in_array($storeCredit->getCustomerId(), $failedCustomerIds)) {
                $errorMessage = __('The customer that was requested does not exist. Verify and try again.');
                $this->addFailedItemStoreCredit($storeCredit, $key, $errorMessage, []);
            }
            $this->checkAmount(storeCredit: $storeCredit, key: $key);
            $this->checkWebsiteId(storeCredit: $storeCredit, key: $key);
        }

        foreach ($this->validationResult->getFailedRowIds() as $id) {
            unset($storeCredits[$id]);
        }

        return $storeCredits;
    }

    /**
     * Check amount.
     * @param StoreCreditInterface $storeCredit
     * @param int $key
     * @return void
     */
    private function checkAmount(StoreCreditInterface $storeCredit, int $key): void
    {
        if (null === $storeCredit->getAmount() || $storeCredit->getAmount() < 0) {
            $errorMessage = __('Invalid amount = %amount. ');
            $this->addFailedItemStoreCredit($storeCredit, $key, $errorMessage, []);
        }
    }

    /**
     * Check website.
     * @param StoreCreditInterface $storeCredit
     * @param int $key
     * @return void
     */
    private function checkWebsiteId(StoreCreditInterface $storeCredit, int $key): void
    {
        try {
            $this->websiteRepository->getById($storeCredit->getWebsiteId());
        } catch (Exception $e) {
            $errorMessage = __('The website with id %1 that was requested was not found. Verify the website and try again.', $storeCredit->getWebsiteId());
            $this->addFailedItemStoreCredit($storeCredit, $key, $errorMessage, []);
        }
    }

    /**
     * Adds failed item to validation result
     *
     * @param StoreCreditInterface $storeCredit
     * @param int $key
     * @param string $message
     * @param array $firstParam
     * @return void
     */
    private function addFailedItemStoreCredit(
        StoreCreditInterface $storeCredit,
        int                  $key,
        string               $message,
        array                $firstParam
    ): void {
        $additionalInfo = [];
        if ($firstParam) {
            $additionalInfo = array_merge($additionalInfo, $firstParam);
        }

        $additionalInfo['customer_id'] = $storeCredit->getCustomerId();
        $additionalInfo['website_id'] = $storeCredit->getWebsiteId();
        $additionalInfo['amount'] = $storeCredit->getAmount();

        $this->validationResult->addFailedItem($key, __($message, $additionalInfo), $additionalInfo);
    }
}
