<?php

declare(strict_types=1);

namespace Mollie\WooCommerce\Payment;

use Mollie\Api\Exceptions\ApiException;
use Mollie\WooCommerce\SDK\Api;
use Mollie\WooCommerce\Settings\Settings;
use Mollie\WooCommerce\Shared\Data;

class PaymentFactory
{
    /**
     * @var Data
     */
    protected $dataHelper;
    /**
     * @var Api
     */
    protected $apiHelper;
    protected $settingsHelper;
    /**
     * @var string
     */
    protected $pluginId;
    protected $logger;

    /**
     * PaymentFactory constructor.
     */
    public function __construct(Data $dataHelper, Api $apiHelper, Settings $settingsHelper, string $pluginId, $logger)
    {
        $this->dataHelper = $dataHelper;
        $this->apiHelper = $apiHelper;
        $this->settingsHelper = $settingsHelper;
        $this->pluginId = $pluginId;
        $this->logger = $logger;
    }

    /**
     * @param $data
     * @return bool|MollieOrder|MolliePayment
     * @throws ApiException
     */
    public function getPaymentObject($data)
    {
        if (
            (!is_object($data) && $data === 'order')
            || (!is_object($data) && strpos($data, 'ord_') !== false)
            || (is_object($data) && $data->resource == 'order')
        ) {
            $refundLineItemsBuilder = new RefundLineItemsBuilder($this->dataHelper);
            $testMode = $this->settingsHelper->isTestModeEnabled();
            $apiKey = $this->settingsHelper->getApiKey($testMode);
            $orderItemsRefunded = new OrderItemsRefunder(
                $refundLineItemsBuilder,
                $this->dataHelper,
                $this->apiHelper->getApiClient($apiKey)->orders
            );

            return new MollieOrder(
                $orderItemsRefunded,
                $data,
                $this->pluginId,
                $this->apiHelper,
                $this->settingsHelper,
                $this->dataHelper,
                $this->logger
            );
        }

        if (
            (!is_object($data) && $data === 'payment')
            || (!is_object($data) && strpos($data, 'tr_') !== false)
            || (is_object($data) && $data->resource == 'payment')
        ) {
            return new MolliePayment(
                $data,
                $this->pluginId,
                $this->apiHelper,
                $this->settingsHelper,
                $this->dataHelper
            );
        }

        return false;
    }
}
