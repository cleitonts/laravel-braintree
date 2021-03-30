<?php

namespace Tests\Unit;

use App\Providers\BraintreeServiceProvider;
use Braintree\Transaction;
use PHPUnit\Framework\TestCase;
use Tests\CreatesApplication;

class TransactionTest extends TestCase
{
    use CreatesApplication;

    private $gateway;
    private $btservice;

    public function __construct(?string $name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        self::createApplication();

        parent::setUp();
        $this->btservice = new BraintreeServiceProvider();
        $this->gateway = $this->btservice->getGateway();
    }

    public function testVoided()
    {
        $result = $this->gateway->transaction()->sale([
            'amount' => '100.00',
            'paymentMethodNonce' => 'fake-valid-no-billing-address-nonce',
            'options' => [
                'submitForSettlement' => True
            ]
        ]);
        $result = $this->gateway->transaction()->void($result->transaction->id);
        $this->assertEquals(Transaction::VOIDED, $result->transaction->status);
    }

    public function testSettlement_declined()
    {
        $result = $this->gateway->transaction()->sale([
            'amount' => '20.00',
            'paymentMethodNonce' => 'fake-valid-no-billing-address-nonce',
            'options' => [
                'submitForSettlement' => True
            ]
        ]);
        $transaction = $this->gateway->testing()->settlementDecline($result->transaction->id);
        $this->assertEquals(Transaction::SETTLEMENT_DECLINED, $transaction->status);
    }

    public function testSubmitted_for_settlement()
    {
        $result = $this->gateway->transaction()->sale([
            'amount' => '11.00',
            'paymentMethodNonce' => 'fake-valid-no-billing-address-nonce',
            'options' => [
                'submitForSettlement' => True
            ]
        ]);
        $this->assertEquals(Transaction::SUBMITTED_FOR_SETTLEMENT, $result->transaction->status);
    }

    public function testSettled()
    {
        $result = $this->gateway->transaction()->sale([
            'amount' => '12.00',
            'paymentMethodNonce' => 'fake-valid-no-billing-address-nonce',
            'options' => [
                'submitForSettlement' => True
            ]
        ]);

        $transaction = $this->gateway->testing()->settle($result->transaction->id);

        $this->assertEquals(Transaction::SETTLED, $transaction->status);
    }

    public function testGatewayRejected()
    {
        $result = $this->gateway->transaction()->sale([
            'amount' => '5001.00',
            'paymentMethodNonce' => 'fake-valid-no-billing-address-nonce',
        ]);
        $this->assertEquals(Transaction::GATEWAY_REJECTED, $result->transaction->status);
    }

    public function testFailed()
    {
        $result = $this->gateway->transaction()->sale([
            'amount' => '3000.00',
            'paymentMethodNonce' => 'fake-valid-no-billing-address-nonce',
        ]);
        $this->assertEquals(Transaction::FAILED, $result->transaction->status);
    }

    public function testAuthorized()
    {
        $result = $this->gateway->transaction()->sale([
            'amount' => '10.00',
            'paymentMethodNonce' => 'fake-valid-no-billing-address-nonce',
        ]);
        $this->assertEquals(Transaction::AUTHORIZED, $result->transaction->status);
    }

    public function testProcessor_declined()
    {
        $result = $this->gateway->transaction()->sale([
            'amount' => '2999.99',
            'paymentMethodNonce' => 'fake-valid-no-billing-address-nonce',
        ]);
        $this->assertEquals(Transaction::PROCESSOR_DECLINED, $result->transaction->status);
    }
}
