<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Subscription;
use App\Models\Transactions;
use App\Models\User;
use App\Providers\BraintreeServiceProvider;
use Braintree\Exception\NotFound;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TransactionController extends Controller
{
    use Utils;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the new transaction screen.
     *
     * @return Renderable
     */
    public function new()
    {
        $btservice = new BraintreeServiceProvider();
        $gateway = $btservice->getGateway();

        $clientToken = $gateway->clientToken()->generate();
        $user = Auth::user();

        return view('new', compact("clientToken", 'user'));
    }

    /**
     * Show the application home.
     *
     * @return Renderable
     * @throws NotFound
     */
    public function index()
    {
        $transactions = Transactions::getListByActiveUser();

        $btservice = new BraintreeServiceProvider();
        $gateway = $btservice->getGateway();

        $plans = $gateway->plan()->all();

        $subsctiption_list = Subscription::getListByActiveUser();
        $list_subscripion = [];
        foreach ($subsctiption_list as $r){
            $list_subscripion[] = $gateway->subscription()->find($r->code);
        }

        $arr_transactions = [];
        foreach ($transactions as $t){
            $arr_transactions[] = $gateway->transaction()->find($t->code);
        }

        return view('home', compact("arr_transactions", "plans", "list_subscripion"));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return false|string
     * @throws NotFound
     */
    public function subscriptionStore(Request $request)
    {
        $data = $request->all();

        $valid = self::fastValidade(['plan' => 'required'], $data);

        if($valid !== false){
            return $valid;
        }

        $btservice = new BraintreeServiceProvider();
        $gateway = $btservice->getGateway();

        $local_customer = Customer::where('gateway_id', 1)->where('user_id', Auth::user()->id)->first();
        $customer = $gateway->customer()->find($local_customer->code);

        $payment = $customer->paymentMethods[0]->token;

        $result = $gateway->subscription()->create([
            'paymentMethodToken' => $payment,
            'planId' => $data["plan"]
        ]);

        if ($result->success || !is_null($result->subscription)) {
            // atualiza a tabela transacao
            Subscription::create([
                'code' => $result->subscription->id,
                'customer_id' => $local_customer->id,
            ]);
            return json_encode([
                "status" => true,
                "message" => $result->subscription->id
            ]);

        } else {
            $errorString = "";
            foreach($result->errors->deepAll() as $error) {
                $errorString .= 'Error: ' . $error->code . ": " . $error->message . "\n";
            }

            return json_encode([
                "status" => false,
                "message" => 'Error: ' . $error->code . ": " . $error->message
            ]);
        }
    }

    /**
     * cancel subscriptions and disable on db
     * @param Request $request
     * @return false|string
     */
    public function subscriptionCancel(Request $request)
    {
        $data = $request->all();

        $valid = self::fastValidade(["code" => "required"], $data);
        if($valid !== false){
            return $valid;
        }

        $btservice = new BraintreeServiceProvider();
        $gateway = $btservice->getGateway();
        $result = $gateway->subscription()->cancel($data['code']);

        if(!$result->success){
            foreach($result->errors->deepAll() AS $error) {
                $error .= $error->code . ": " . $error->message . "\n";
            }
            return json_encode([
                "status" => false,
                "message" => $error
            ]);
        }

        Subscription::where('code', $data['code'])->delete();

        return json_encode([
            "status" => true,
            "message" => $result->subscription->id
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return false|string
     */
    public function store(Request $request)
    {
        $data = $request->all();

        $valid = self::fastValidade([
            'name' => 'required',
            'amount' => 'required',
            'email' => 'required',
            'nonce' => 'required',
            'phone' => 'required',
        ], $data);
        if($valid !== false){
            return $valid;
        }

        $amount = $data['amount'];
        $nonce = $data['nonce'];

        /**
         * @var User $user
         */
        $user = Auth::user();
        $user->update([
            'name' => $data["name"],
            'email' => $data["email"],
            'phone' => $data["phone"]
        ]);

        $btservice = new BraintreeServiceProvider();
        $gateway = $btservice->getGateway();

        $customer = Customer::where('gateway_id', 1)->where('user_id', $user->id)->first();

        if(!$customer){
            $name = explode(' ', $data["name"]);
            $result = $gateway->customer()->create([
                'firstName' => $name[0],
                'lastName' => end($name),
                'email' => $data["email"],
                'phone' => $data["phone"]
            ]);

            if($result->success){
                $customer = Customer::create([
                    'code' => $result->customer->id,
                    'user_id' => $user->id,
                    'gateway_id' => 1
                ]);

                $payment = $gateway->paymentMethod()->create([
                    'customerId' => $customer->code,
                    'paymentMethodNonce' => $nonce
                ]);
                if(!$payment->success){
                    foreach($payment->errors->deepAll() AS $error) {
                        $error .= $error->code . ": " . $error->message . "\n";
                    }
                    return json_encode([
                        "status" => false,
                        "message" => $error
                    ]);
                }
            }
            else{
                $error = '';
                foreach($result->errors->deepAll() AS $error) {
                    $error .= $error->code . ": " . $error->message . "\n";
                }
                return json_encode([
                    "status" => false,
                    "message" => $error
                ]);
            }
        }

        $result = $gateway->transaction()->sale([
            'amount' => $amount,
            'paymentMethodNonce' => $nonce,
            'customerId' => $customer->code,
            'options' => [
                'submitForSettlement' => True
            ]
        ]);

        if ($result->success || !is_null($result->transaction)) {
            // atualiza a tabela transacao
            Transactions::create([
                'code' => $result->transaction->id,
                'status_id' => 1,
                'customer_id' => $customer->id,
            ]);
            return json_encode([
                "status" => true,
                "message" => $result->transaction->id
            ]);

        } else {
            $errorString = "";
            foreach($result->errors->deepAll() as $error) {
                $errorString .= 'Error: ' . $error->code . ": " . $error->message . "\n";
            }

            return json_encode([
                "status" => false,
                "message" => 'Error: ' . $error->code . ": " . $error->message
            ]);
        }
    }
}
