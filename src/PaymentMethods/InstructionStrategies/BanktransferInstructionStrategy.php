<?php

declare(strict_types=1);

namespace Mollie\WooCommerce\PaymentMethods\InstructionStrategies;

use Mollie\WooCommerce\Plugin;

class BanktransferInstructionStrategy implements InstructionStrategyI
{

    public function execute(
        $gateway,
        $payment,
        $order,
        $admin_instructions = false
    ) {
        $instructions = '';

        if (!$payment->details) {
            return null;
        }

        if ($payment->isPaid()) {
            $instructions .= sprintf(
            /* translators: Placeholder 1: consumer name, placeholder 2: consumer IBAN, placeholder 3: consumer BIC */
                __('Payment completed by <strong>%1$s</strong> (IBAN (last 4 digits): %2$s, BIC: %3$s)', 'mollie-payments-for-woocommerce'),
                $payment->details->consumerName,
                substr($payment->details->consumerAccount, -4),
                $payment->details->consumerBic
            );
        } elseif ($order->has_status('on-hold') || $order->has_status('pending')) {
            if (!$admin_instructions) {
                $instructions .= __('Please complete your payment by transferring the total amount to the following bank account:', 'mollie-payments-for-woocommerce') . "\n\n\n";
            }

            /* translators: Placeholder 1: 'Stichting Mollie Payments' */
            $instructions .= sprintf(__('Beneficiary: %s', 'mollie-payments-for-woocommerce'), $payment->details->bankName) . "\n";
            $instructions .= sprintf(__('IBAN: <strong>%s</strong>', 'mollie-payments-for-woocommerce'), implode(' ', str_split($payment->details->bankAccount, 4))) . "\n";
            $instructions .= sprintf(__('BIC: %s', 'mollie-payments-for-woocommerce'), $payment->details->bankBic) . "\n";

            if ($admin_instructions) {
                /* translators: Placeholder 1: Payment reference e.g. RF49-0000-4716-6216 (SEPA) or +++513/7587/59959+++ (Belgium) */
                $instructions .= sprintf(__('Payment reference: %s', 'mollie-payments-for-woocommerce'), $payment->details->transferReference) . "\n";
            } else {
                /* translators: Placeholder 1: Payment reference e.g. RF49-0000-4716-6216 (SEPA) or +++513/7587/59959+++ (Belgium) */
                $instructions .= sprintf(__('Please provide the payment reference <strong>%s</strong>', 'mollie-payments-for-woocommerce'), $payment->details->transferReference) . "\n";
            }

            if (!empty($payment->expiresAt)) {
                $expiryDate = $payment->expiresAt;
                //$this->logger->log(LogLevel::DEBUG, "Due date assigned: {$expiryDate}");
                $expiryDate = date_i18n(wc_date_format(), strtotime($expiryDate));

                if ($admin_instructions) {
                    $instructions .= "\n" . sprintf(
                            __('The payment will expire on <strong>%s</strong>.', 'mollie-payments-for-woocommerce'),
                            $expiryDate
                        ) . "\n";
                } else {
                    $instructions .= "\n" . sprintf(
                            __('The payment will expire on <strong>%s</strong>. Please make sure you transfer the total amount before this date.', 'mollie-payments-for-woocommerce'),
                            $expiryDate
                        ) . "\n";
                }
            }
        }

        return $instructions;
    }
}
