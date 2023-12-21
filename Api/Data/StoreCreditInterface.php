<?php

namespace Vbdev\StoreCredit\Api\Data;

/**
 * Customer Store Credit Interface is used to encapsulate data that can be processed by efficient Store Credit API.
 * @api
 */
interface StoreCreditInterface
{
    /**#@+
     * Constants
     */
    const CUSTOMER_ID = 'customer_id';
    const WEBSITE_ID = 'website_id';
    const AMOUNT = 'amount';
    /**#@-*/

    /**
     * Set Customer ID Store Credit value.
     *
     * @param int $customerId
     * @return $this
     */
    public function setCustomerId($customerId);

    /**
     * Get Customer ID Store Credit value.
     *
     * @return int
     */
    public function getCustomerId();

    /**
     * Set ID of website, that contains Store Credit value.
     *
     * @param int $websiteId
     * @return $this
     */
    public function setWebsiteId($websiteId);

    /**
     * Get ID of website, that contains Store Credit value.
     *
     * @return int
     */
    public function getWebsiteId();

    /**
     * Set Amount for Store Credit.
     *
     * @param double $amount
     * @return $this
     */
    public function setAmount($amount);

    /**
     * Get Amount of Store Credit.
     *
     * @return double
     */
    public function getAmount();
}
