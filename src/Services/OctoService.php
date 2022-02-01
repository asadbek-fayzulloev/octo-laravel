<?php

namespace Asadbek\OctoLaravel\Services;

use Asadbek\OctoLaravel\Models\OctoTransactions;
use Asadbek\OctoLaravel\Models\Order;
use Asadbek\OctoLaravel\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;

class OctoService
{
    protected $url;
    protected Order $order;
    protected User $user;
    protected $payload;
    public function __construct(Order $order, $payload){
        $this->order = $order;
        $this->user = $order->user()->first();
        $this->url = 'https://secure.octo.uz/';
        $this->notify_url = 'https://www.google.com';
        $this->return_url = 'https://www.google.com';


        return ;
    }
    public function prepare(Order $order, User $user)
    {
        $octoTransaction = OctoTransactions::where('order_id', $order->id)->first();
        if ($order) {
            return -1;
        }

    }

    public function pay($type)
    {
        $this->setMethod('prepare_payment');


    }

    public function verify()
    {

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
    public function setPayload()
    {
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
            CURLOPT_POSTFIELDS => $this->makeRequest(Arr::get($payload, 'basket')),
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
    protected function makeRequest(array $basket, $payment_methods = [["method"=>"bank_card"]] )
    {
        $request = '{
            "octo_shop_id": ' . config('octo.octo_shop_id') . ',
            "octo_secret": ' . config('octo.octo_secret') . ',
            "shop_transaction_id": ' . $this->order->id . ',
            "auto_capture": ' . config('octo.auto_capture') . ',
            "test": ' . config('octo.test', false) . ',
            "init_time": ' . NOW() . ',
            "user_data": ' . json_encode($this->user) . ',
            "total_sum": 103.33,
            "currency": ' . config('octo.currency') . ',
                "tag": "ticket",
                "description": ' . $this->getDescription() . ',
              "basket": '.json_encode($basket).',
              "payment_methods": '.json_encode($payment_methods).'
              "tsp_id":18,
              "return_url": ' . $this->notify_url . ',
              "notify_url": ' . $this->return_url . ',
              "language": ' . config('octo.locale', 'en') . ',
              "ttl": 15
            }';

        return $request;
    }

    private function getDescription()
    {
        return "Payment".$this->order->id;
    }
}
