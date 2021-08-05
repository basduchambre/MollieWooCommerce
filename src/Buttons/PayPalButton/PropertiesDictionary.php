<?php

declare(strict_types=1);

namespace Mollie\WooCommerce\Buttons\PayPalButton;

class PropertiesDictionary
{
    const CREATE_ORDER_SINGLE_PROD_REQUIRED_FIELDS
        = [
            PropertiesDictionary::NONCE,
            PropertiesDictionary::PRODUCT_ID,
            self::PRODUCT_QUANTITY
        ];
    const CREATE_ORDER_CART_REQUIRED_FIELDS
        = [
            PropertiesDictionary::NONCE
        ];

    const PRODUCT_ID = 'productId';
    const NONCE = 'nonce';
    const PRODUCT_QUANTITY = 'productQuantity';
    const CALLER_PAGE = 'callerPage';
    const NEED_SHIPPING = 'needShipping';
    const CREATE_ORDER = 'mollie_paypal_create_order';
    const CREATE_ORDER_CART = 'mollie_paypal_create_order_cart';
    const UPDATE_AMOUNT = 'mollie_paypal_update_amount';
    const REDIRECT = 'mollie_paypal_redirect';
}
