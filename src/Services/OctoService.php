<?php

namespace Asadbek\OctoLaravel\Services;

use Asadbek\OctoLaravel\Http\Classes\OctoResponse;
use Asadbek\OctoLaravel\Models\OctoTransactions;
use Asadbek\OctoLaravel\Models\Order;
use Asadbek\OctoLaravel\Models\User;
use Asadbek\OctoLaravel\Requests\OctoRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;

class OctoService
{
    protected $url;
    protected Order $order;
    protected User $user;
    protected $payload;
    private CbuUzService $currencyService;
    private $order_price;
    private $payment_uuid;
    private $redirect_after_verification;
    private OctoResponse $response;
    public function __construct($notify_url, $return_url, $redirect_after_verification){
        $this->url = 'https://secure.octo.uz/';
        $this->currencyService = new CbuUzService();
        $this->notify_url = $notify_url;
        $this->return_url = $return_url;
        $this->redirect_after_verification = $redirect_after_verification;
    }
    private function setPrice(){
        $result = $this->currencyService->getOneByDate("USD", NOW());
        $rate = Arr::get($result, 'rate');
        $rate = intval($rate/100)*100;
        $this->order_price = $this->order->price * $rate;
    }
    public function setDetails(Order $order){
        $this->order = $order;
        $this->user = $order->user()->first();
    }
    public function prepare(Order $order, User $user)
    {
        $octoTransaction = OctoTransactions::where('order_id', $order->id)->first();
        if ($order) {
            return -1;
        }

    }
    public function pay($type="Visa")
    {
        $this->setMethod('prepare_payment');
        $result = $this->send();
        $this->response = new OctoResponse($result);
        $url = $this->response->octo_pay_url;
        if($this->response->error==0){
            switch ($this->response->status){
                case "created": {
                    $this->createOrder();
                } break;
                case "confirmed": {

                } break;
                case "waiting_for_capture": {

                } break;
                case "succeeded": {
                    $this->order->paid = $type;
                    $this->order->save();
                    $this->createOrder();
                } break;
                default: {

                } break;
            }
            $this->payment_uuid = $this->response->octo_payment_UUID;

        }
        return $url;
    }

    public function verify()
    {
        $this->setMethod('prepare_payment');
        $result = $this->send();
        $this->response = new OctoResponse($result);
        if($this->response->error==0){
            switch ($this->response->status){
                case "created": {
                    $this->createOrder();
                } break;
                case "confirmed": {

                } break;
                case "waiting_for_capture": {

                } break;
                case "succeeded": {
                    $this->createOrder();
                } break;
                default: {

                } break;
            }
            $this->payment_uuid = $this->response->octo_payment_UUID;

        }
        return redirect()->route($this->redirect_after_verification);
    }

    public function notify()
    {

    }

    public function validate(Request $request, array $rules, array $messages = [], array $customAttributes = [])
    {
    }


    public function setMethod($method)
    {
        $this->payload['method'] = $method;
    }
    public function setPayload(OctoRequest $request)
    {
        $this->payload = $request;
    }

    /**
     * Отпраляет одно sms сообщение.
     *
     * @param $phone
     * @param $message
     * @return mixed
     */
    public function send()
    {
        return $this->request($this->payload);
    }


    /**
     * Отправить запрос.
     *
     * @param $data
     * @return mixed
     */
    protected function request($payload)
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $this->url.'/'.Arr::get($payload, 'method'),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $this->makeRequest(),
            CURLOPT_HTTPHEADER => array(
                'Content-type: application/json'
            ),
        ));
        $response = curl_exec($curl);
        $err = curl_error($curl);
        Log::info('OctoException' . $err);
        curl_close($curl);

        return $response;
    }

    /**
     * Сформировать запрос.
     *
     * @param array $messages
     * @return string
     */
    protected function makeRequest($payment_methods = [["method"=>"bank_card"]] )
    {
        $basket = [
            "position_desc" => "Booking #".$this->order->id,
            "count"=> 1,
            "price" => $this->order_price,
            "supplier_shop_id" => $this->order->id
        ];
        $request = '{
            "octo_shop_id": ' . config('octo.octo_shop_id') . ',
            "octo_secret": ' . config('octo.octo_secret') . ',
            "shop_transaction_id": ' . $this->order->id . ',
            "auto_capture": ' . config('octo.auto_capture') . ',
            "test": ' . config('octo.test', false) . ',
            "init_time": ' . NOW() . ',
            "user_data": ' . json_encode($this->user) . ',
            "total_sum": '. $this->order_price.'
            "currency": ' . config('octo.currency') . ',
                "tag": "ticket",
                "description": ' . $this->getDescription() . ',
              "basket": '.json_encode($this->order).',
              "payment_methods": '.json_encode($payment_methods).'
              "tsp_id":18,
              "return_url": ' . $this->notify_url . ',
              "notify_url": ' . $this->return_url . ',
              "language": ' . config('octo.locale', 'en') . ',
              "ttl": 15
            }';

        return $request;
    }

    private function createOrder(){
        $transaction = OctoTransactions::where('shop_transaction_id', $this->order->id)->firstOrCreate();
        $transaction->user_id = $this->user->id;
        $transaction->price = $this->order_price;
        $transaction->octo_payment_UUID = $this->response->octo_payment_UUID;
        $transaction->status = $this->response->status;
        $transaction->octo_pay_url = $this->response->octo_pay_url;
        $transaction->currency = "UZS";
        $transaction->save();
        return 0;
    }
    private function getDescription()
    {
        return "Payment".$this->order->id;
    }
}
