# magento2-store-credit

    composer require vbdev/magento2-store-credit

## Main Functionalities

- The module offers the possibility to create credits for the customer with rest API, this credit can be used in the store as a
  payment method.

## Install

### Type 1: Zip file

- Unzip the zip file in `app/code/Vbdev`
- Enable the module by running `bin/magento module:enable Vbdev_StoreCredit`
- Apply database updates by running `bin/magento setup:upgrade`
- Flush the cache by running `bin/magento cache:flush`

### Type 2: Composer

- Install the module composer by running `composer require vbdev/magento2-store-credit`
- enable the module by running `bin/magento module:enable Vbdev_StoreCredit`
- apply database updates by running `bin/magento setup:upgrade`
- Flush the cache by running `bin/magento cache:flush`

## Manage store credits

REST Endpoints:

**POST <base_url>/rest/V1/store-credit/get**

- Returns the customer's store credit. If the customer ID is not found, it will be ignored and only those found will be
  returned. If none of the customer IDs are found, an empty array is returned.
- BODY EXAMPLE:

```yaml
{
    "customerIds": [
        "94795",
        "94796",
        "94797"
    ]
}
```

**POST <base_url>/rest/V1/store-credit/create**

- Create store credit for a customer. If any item has an invalid customer_id, website_id, or value, it will be marked as
  failed and deleted from the list, and an error message describing the problem will be returned. If there are no failed
  items during the update, an empty array is returned. If an error occurred during the update, an exception will be
  thrown.
- BODY EXAMPLE:

```yaml
{
  {
      "storeCredits": [
          {
              "customer_id": "94795",
              "website_id": "4",
              "amount": "234.12"
          },
          {
              "customer_id": "94796",
              "website_id": "12",
              "amount": "903.12"
          },
          {
              "customer_id": "94797",
              "website_id": "1",
              "amount": "200.12"
          }
      ]
  }
}
```

- When making a request for a customer ID that does not have Store Credit, it will create a new one with the “amount”
  value entered and for that website entered.
- If a request is made for a customer ID that already has a Store Credit, it will be updated to the value entered in the
  "amount".
- There is also the possibility of adding the values, that is, if they already have a Store Credit, when making another
  request the credit will be added to the previous one and not replaced, for this use UPDATE:

**POST <base_url>/rest/V1/store-credit/update**

- BODY EXAMPLE:
- Update customer's store credit. If any items will have invalid customer_id, website_id or amount, they will be marked
  as failed and excluded from update list and an error message describing the problem will be returned. If there were no
  failed items during update empty array will be returned. If error
  occurred during the update, an exception will be thrown.

```yaml
{
    "storeCredits": [
        {
            "customer_id": "94795",
            "website_id": "4",
            "amount": "4"
        },
        {
            "customer_id": "94796",
            "website_id": "1",
            "amount": "4"
        },
        {
            "customer_id": "94797",
            "website_id": "5",
            "amount": "4"
        }
    ]
}

```







