<?php
require '../../vendor/autoload.php';

use \Genesis\Genesis as Genesis;
use \Genesis\Config as GenesisConfig;

GenesisConfig::loadSettings('../../config/default.ini');

try {
    $genesis = new Genesis('WPF\Create');

    // Params
    $genesis
        ->request()
            ->setTransactionId($_POST['transaction_id'])
            ->setCurrency($_POST['currency'])
            ->setAmount($_POST['amount'])
            ->setUsage($_POST['usage'])
            ->setDescription($_POST['description'])
            ->setCustomerEmail($_POST['customer_email'])
            ->setCustomerPhone($_POST['customer_phone']);
    // URLs
    $genesis
        ->request()
            ->setNotificationUrl($_POST['notification_url'])
            ->setReturnSuccessUrl($_POST['return_success_url'])
            ->setReturnFailureUrl($_POST['return_failure_url'])
            ->setReturnCancelUrl($_POST['return_cancel_url']);
    // Billing
    $genesis
        ->request()
            ->setBillingFirstName($_POST['billing_address']['first_name'])
            ->setBillingLastName($_POST['billing_address']['last_name'])
            ->setBillingAddress1($_POST['billing_address']['address1'])
            ->setBillingZipCode($_POST['billing_address']['zip_code'])
            ->setBillingCity($_POST['billing_address']['city'])
            ->setBillingCountry($_POST['billing_address']['country']);
    // Shipping
    $genesis
        ->request()
            ->setShippingFirstName($_POST['shipping_address']['first_name'])
            ->setShippingLastName($_POST['shipping_address']['last_name'])
            ->setShippingAddress1($_POST['shipping_address']['address1'])
            ->setShippingZipCode($_POST['shipping_address']['zip_code'])
            ->setShippingCity($_POST['shipping_address']['city'])
            ->setShippingCountry($_POST['shipping_address']['country']);
    // Risk
    $genesis
        ->request()
            ->setRiskSsn($_POST['risk_params']['ssn'])
            ->setRiskMacAddress($_POST['risk_params']['mac_address'])
            ->setRiskSessionId($_POST['risk_params']['session_id'])
            ->setRiskUserId($_POST['risk_params']['user_id'])
            ->setRiskUserLevel($_POST['risk_params']['user_level'])
            ->setRiskEmail($_POST['risk_params']['email'])
            ->setRiskPhone($_POST['risk_params']['phone'])
            ->setRiskRemoteIp($_POST['risk_params']['remote_ip'])
            ->setRiskSerialNumber($_POST['risk_params']['serial_number']);

    // Descriptor
    $genesis
        ->request()
            ->setDynamicMerchantName($_POST['dynamic_descriptor']['merchant_name'])
            ->setDynamicMerchantCity($_POST['dynamic_descriptor']['merchant_city']);

    $paymentMethods = array(
        \Genesis\API\Constants\Payment\Methods::EPS         =>
            \Genesis\API\Constants\Transaction\Types::PPRO,
        \Genesis\API\Constants\Payment\Methods::GIRO_PAY    =>
            \Genesis\API\Constants\Transaction\Types::PPRO,
        \Genesis\API\Constants\Payment\Methods::PRZELEWY24  =>
            \Genesis\API\Constants\Transaction\Types::PPRO,
        \Genesis\API\Constants\Payment\Methods::QIWI        =>
            \Genesis\API\Constants\Transaction\Types::PPRO,
        \Genesis\API\Constants\Payment\Methods::SAFETY_PAY  =>
            \Genesis\API\Constants\Transaction\Types::PPRO,
        \Genesis\API\Constants\Payment\Methods::TELEINGRESO =>
            \Genesis\API\Constants\Transaction\Types::PPRO,
        \Genesis\API\Constants\Payment\Methods::TRUST_PAY   =>
            \Genesis\API\Constants\Transaction\Types::PPRO,
    );

    $transactionTypesList = array();

    // Transaction Types
    if (isset($_POST['transaction_type'])) {
        foreach ($_POST['transaction_type'] as $selected_type) {
            if (array_key_exists($selected_type, $paymentMethods)) {
                $transaction_type = $paymentMethods[$selected_type];

                $transactionTypesList[$transaction_type]['name'] = $transaction_type;

                $transactionTypesList[$transaction_type]['parameters'][] = array(
                    'payment_method' => $selected_type
                );
            } else {
                $transactionTypesList[] = $selected_type;
            }
        }

        foreach ($transactionTypesList as $transaction_type) {
            if (is_array($transaction_type)) {
                $genesis->request()->addTransactionType(
                    $transaction_type['name'],
                    $transaction_type['parameters']
                );
            } else {
                if (\Genesis\API\Constants\Transaction\Types::isPayByVoucher($transaction_type)) {
                    $parameters = [
                        'card_type' =>
                            \Genesis\API\Constants\Transaction\Parameters\PayByVouchers\CardTypes::VIRTUAL,
                        'redeem_type' =>
                            \Genesis\API\Constants\Transaction\Parameters\PayByVouchers\RedeemTypes::INSTANT
                    ];
                    $genesis
                        ->request()
                            ->addTransactionType($transaction_type, $parameters);
                } else {
                    $genesis
                        ->request()
                            ->addTransactionType($transaction_type);
                }
            }
        }
    }

    $output = null;

    $genesis->execute();

    $output['request']  = $genesis->request()->getDocument();
    $output['response'] = $genesis->response()->getResponseRaw();
} catch (\Exception $e) {
    $output['response'] = $e->getMessage();
}

echo json_encode($output);

exit(0);
