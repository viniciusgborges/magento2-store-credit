<?php

namespace Vbdev\StoreCredit\Model\ResourceModel\Customer;

use Exception;
use Magento\CustomerBalance\Model\Balance;
use Magento\CustomerBalance\Model\BalanceFactory;
use Magento\CustomerBalance\Model\ResourceModel\Balance as ResourceBalance;
use Magento\CustomerBalance\Model\ResourceModel\Balance\Collection;
use Magento\CustomerBalance\Model\ResourceModel\Balance\CollectionFactory as BalanceCollectionFactory;
use Magento\CustomerBalance\Model\ResourceModel\BalanceFactory as ResourceBalanceFactory;
use Magento\Framework\Exception\AlreadyExistsException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Psr\Log\LoggerInterface;
use Vbdev\StoreCredit\Api\StoreCreditInterface;

class StoreCredit implements StoreCreditInterface
{
    /**
     * @var string
     */
    private const LOG_CONTEXT = 'Store Credit';

    /**
     * @param BalanceFactory $balanceFactory
     * @param ResourceBalanceFactory $resourceBalanceFactory
     * @param BalanceCollectionFactory $collectionFactory
     * @param LoggerInterface $logger
     */
    public function __construct(
        private BalanceFactory           $balanceFactory,
        private ResourceBalanceFactory   $resourceBalanceFactory,
        private BalanceCollectionFactory $collectionFactory,
        private LoggerInterface          $logger
    ) {
    }

    /**
     * @inheritDoc
     */
    public function get(array $customerIds)
    {
        $transformedArrays = array_unique(
            array_map(function ($customerId) {
                return trim($customerId);
            }, preg_split('/\s*,\s*/', $customerIds[0]))
        );
        try {
            $balanceCollection = $this->getBalanceCollection();
            $balanceCollection->addFieldToFilter(
                'customer_id',
                [
                    'in' => $transformedArrays
                ]
            );
        } catch (Exception $e) {
            $this->handleException(exception: $e, action: 'GET');
        }

        return $balanceCollection->getData();
    }

    /**
     * @inheritdoc
     */
    public function create(array $storeCredits): bool
    {
        $storeCreditsLogs = [];
        if (!empty($storeCredits)) {
            /** @var \Vbdev\StoreCredit\Api\Data\StoreCreditInterface $storeCredit */
            foreach ($storeCredits as $storeCredit) {
                $customerId = $storeCredit->getCustomerId();
                $websiteId = $storeCredit->getWebsiteId();
                $amount = $storeCredit->getAmount();
                try {
                    if (!($balanceCollection = $this->checkIfCustomerBalanceExists(customerId: $customerId, websiteId: $websiteId))) {
                        $this->createCustomerBalance(customerId: $customerId, websiteId: $websiteId, amount: $amount);
                        $storeCreditsLogs[] = $this->getBalanceModel()->getData();
                    } elseif ($amount !== $balanceCollection->getFirstItem()->getAmount()) {
                        $balance = $balanceCollection->getFirstItem();
                        $balance->setAmount($amount);
                        /** @var Balance $balance */
                        $this->getBalanceResourceModel()->save($balance);
                        $storeCreditsLogs[] = $balance->getData();
                    }
                } catch (Exception $e) {
                    $this->handleException(exception: $e, action: 'CREATE');
                }
            }
            $this->logSuccess(action: 'CREATE', storeCreditsLogs: $storeCreditsLogs);
            return true;
        }
        $this->logFailure(action: 'CREATE', storeCreditsLogs: $storeCreditsLogs);

        return true;
    }

    /**
     * @inheritdoc
     */
    public function update(array $storeCredits): bool
    {
        $storeCreditsLogs = [];
        if (!empty($storeCredits)) {
            /** @var \Vbdev\StoreCredit\Api\Data\StoreCreditInterface $storeCredit */
            foreach ($storeCredits as $storeCredit) {
                $customerId = $storeCredit->getCustomerId();
                $websiteId = $storeCredit->getWebsiteId();
                $amount = $storeCredit->getAmount();
                try {
                    if ($balanceCollection = $this->checkIfCustomerBalanceExists(customerId: $customerId, websiteId: $websiteId)) {
                        $balance = $balanceCollection->getFirstItem();
                        $balance->setAmountDelta($amount);
                        /** @var Balance $balance */
                        $this->getBalanceResourceModel()->save($balance);
                        $storeCreditsLogs[] = $balance->getData();
                    }
                } catch (Exception $e) {
                    $this->handleException(exception: $e, action: 'UPDATE');
                }
            }
            $this->logSuccess(action: 'UPDATE', storeCreditsLogs: $storeCreditsLogs);
            return true;
        }
        $this->logFailure(action: 'UPDATE', storeCreditsLogs: $storeCreditsLogs);

        return true;
    }

    private function checkIfCustomerBalanceExists(int $customerId, int $websiteId): bool|Collection
    {
        $balanceCollection = $this->getBalanceCollection();
        $balanceCollection->addFieldToFilter('customer_id', $customerId)
            ->addFieldToFilter('website_id', $websiteId);

        return $balanceCollection->getSize() > 0 ? $balanceCollection : false;
    }

    /**
     * @throws AlreadyExistsException
     */
    private function createCustomerBalance(int $customerId, int $websiteId, float $amount): void
    {
        $balance = $this->getBalanceModel();
        $balance->setCustomerId($customerId)
            ->setAmountDelta($amount)
            ->setWebsiteId($websiteId);

        $this->getBalanceResourceModel()->save($balance);
    }

    /**
     * @throws CouldNotSaveException
     */
    private function handleException(Exception $exception, string $action): void
    {
        $this->logger->error(
            self::LOG_CONTEXT . " {$action} Fail Response.",
            ['exceptionCode' => $exception->getCode(), 'exceptionMessage' => __($exception->getMessage())]
        );

        throw new CouldNotSaveException(__('Could not %1 Store Credits.', $action), $exception);
    }

    private function logSuccess(string $action, array $storeCreditsLogs): void
    {
        $this->logger->info(self::LOG_CONTEXT . " {$action} request completed successfully.", $storeCreditsLogs);
    }

    private function logFailure(string $action, array $storeCreditsLogs): void
    {
        $this->logger->info(self::LOG_CONTEXT . " {$action} request failure.", $storeCreditsLogs);
    }

    public function getBalanceModel(): Balance
    {
        return $this->balanceFactory->create();
    }

    protected function getBalanceResourceModel(): ResourceBalance
    {
        return $this->resourceBalanceFactory->create();
    }

    protected function getBalanceCollection(): Collection
    {
        return $this->collectionFactory->create();
    }
}
