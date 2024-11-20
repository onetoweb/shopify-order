<?php

namespace Onetoweb\ShopifyOrder\Graph;

/**
 * Order Graph.
 */
trait Order
{
    /**
     * Full order graph
     * 
     * @return string
     */
    public static function fullOrder(): string
    {
        return <<<GRAPH
{
    id
    name
    createdAt
    closedAt
    processedAt
    note
    phone
    poNumber
    returnStatus
    currentTotalPriceSet {
        shopMoney {
            amount
        }
    }
    shippingAddress {
        company
        firstName
        lastName
        address1
        address2
        city
        zip
        countryCode
        phone
    }
    lineItems(first: 250) {
        edges {
            node {
                sku
                name
                currentQuantity
                discountedUnitPriceSet {
                    shopMoney {
                        amount
                    }
                }
            }
        }
    }
}
GRAPH;
    }
}
