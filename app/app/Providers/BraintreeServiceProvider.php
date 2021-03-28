<?php


namespace App\Providers;


use Braintree\Gateway;
use Braintree\Transaction;
use Exception;

/**
 * Class BraintreeServiceProvider
 * @package App\Providers
 */
class BraintreeServiceProvider
{
    public static $transactionSuccessStatuses = [
        Transaction::AUTHORIZED,
        Transaction::AUTHORIZING,
        Transaction::SETTLED,
        Transaction::SETTLING,
        Transaction::SETTLEMENT_CONFIRMED,
        Transaction::SETTLEMENT_PENDING,
        Transaction::SUBMITTED_FOR_SETTLEMENT
    ];

    private $gateway;

    /**
     * BraintreeServiceProvider constructor.
     * @throws Exception
     */
    public function __construct()
    {
        $gateway = \App\Models\Gateway::where('name', 'Braintree')->first();

        if(!$gateway){
            throw new Exception("The first item of gateway table must be config data of Braintree. And the name must be 'Braintree'.");
        }

        if (empty($gateway['environment']) || empty($gateway['merchant_id']) || empty($gateway['public_key']) || empty($gateway['private_key'])) {
            throw new Exception('Cannot find necessary environmental variables. See https://github.com/braintree/braintree_php_example#setup-instructions for instructions');
        }

        $this->gateway = new Gateway([
            'environment' => $gateway['environment'],
            'merchantId' => $gateway['merchant_id'],
            'publicKey' => $gateway['public_key'],
            'privateKey' => $gateway['private_key']
        ]);
    }

    /**
     * @return mixed
     */
    public function getGateway()
    {
        return $this->gateway;
    }
}
