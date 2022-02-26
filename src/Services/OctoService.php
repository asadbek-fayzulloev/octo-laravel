<?php

namespace Asadbek\OctoLaravel\Services;

use Asadbek\OctoLaravel\Http\Classes\OctoResponse;
use Asadbek\OctoLaravel\Models\OctoTransactions;
use Asadbek\OctoLaravel\Models\Order;
use Asadbek\OctoLaravel\Models\User;
use Asadbek\OctoLaravel\Requests\OctoRequest;
use Carbon\Carbon;
use Exception;
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
    private $type;
    private $redirect_after_verification;
    private OctoResponse $response;

    public function __construct($redirect_after_verification)
    {
        $this->url = 'https://secure.octo.uz/';
        $this->currencyService = new CbuUzService();
        $this->redirect_after_verification = $redirect_after_verification;
    }
    public function setType($type){
        $this->type = $type;

    }
    private function setPrice()
    {
        $result = $this->currencyService->getOneByDate("USD", NOW());
        $rate = Arr::get($result, 'rate');
        $rate = intval($rate / 100) * 100;
        $this->order_price = $this->order->price * $rate;
    }

    public function setDetails($order_id): void
    {
        $this->order = Order::where('id', $order_id)->first();
        $this->user = User::where('id', $this->order->user_id)->first();
        $this->setPrice();

    }
    public function setNotifyUrl($notify_url){
        $this->notify_url = $notify_url;
    }
    public function setReturnUrl($return_url){
        $this->return_url = $return_url;
    }
    public function prepare(): string
    {
        $this->setMethod('prepare_payment');
        $result = json_decode($this->send(), true);
        if($result["error"]!=0){
            Log::error("Something went wrong! Octo Error: ".$result["error"]);
            dd($result);
            return $this->return_url;
        }
        $this->response = new OctoResponse($result);
        return $this->response->octo_pay_url;
    }

    public function pay(): string
    {
        $url = $this->prepare();
        $this->payment_uuid = $this->response->octo_payment_UUID;

        if ($this->response->error == 0) {
            switch ($this->response->status) {
                case "created":
                    {
                        $this->createOrder();
                    }
                    break;
                case "confirmed":
                    {

                    }
                    break;
                case "waiting_for_capture":
                    {

                    }
                    break;
                case "succeeded":
                    {
                        $this->order->paid = $this->type;
                        $this->order->save();
                        $this->createOrder();
                    }
                    break;
                default:
                    {

                    }
                    break;
            }

        }
        return $url;
    }

    public function verify(): string
    {
        $this->prepare();
        $this->payment_uuid = $this->response->octo_payment_UUID;
        if ($this->response->error == 0) {
            switch ($this->response->status) {
                case "created":
                    {
                        $this->createOrder();
                    }
                    break;

                case "succeeded":
                    {
                        $this->order->paid = $this->type;
                        $this->order->save();
                        $this->createOrder();
                    }
                    break;
                default:
                    {

                    }
                    break;
            }
            $this->payment_uuid = $this->response->octo_payment_UUID;

        }
        return $this->redirect_after_verification;
    }

    public function notify() : string
    {
        $url = $this->prepare();
        if ($this->response->error == 0) {
            switch ($this->response->status) {
                case "created":
                    {
                        $this->createOrder();
                    }
                    break;

                case "succeeded":
                    {
                        $this->order->paid = $this->type;
                        $this->order->save();
                        $this->createOrder();
                    }
                    break;
                default:
                    {

                    }
                    break;
            }
        }
        return $url;
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
            CURLOPT_URL => $this->url . '/' . Arr::get($payload, 'method'),
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
    protected function makeRequest($payment_methods = [['method' => 'bank_card']])
    {
        $basket[] = [
            "position_desc" => "Booking #" . $this->order->id,
            "count" => 1,
            "price" => $this->order_price,
//            "supplier_shop_id" => config('octo.octo_shop_id')
        ];
        $users = [
            "user_id" => $this->user->id ?? 0,
            "phone" => $this->user->phone ?? "",
            "email" => $this->user->email ?? "user@mail.com",
        ];
//        dd(Carbon::now()->format('Y-m-d H:m:s.u'));
        $basket = strval(json_encode($basket));

        $request = '{
            "octo_shop_id": ' . config('octo.octo_shop_id') . ',
            "octo_secret": ' . config('octo.octo_secret') . ',
            "shop_transaction_id": ' . $this->order->id . ',
            "auto_capture": ' . config('octo.auto_capture') . ',
            "test": ' . config('octo.test', false) . ',
            "init_time": "' . strval(Carbon::now()->format('Y-m-d H:m:s')) . '",
            "user_data": '.json_encode($users).',
            "total_sum": ' . $this->order_price . ',
            "currency": ' . config('octo.currency') . ',
                "tag": "ticket",
                "description": "' . $this->getDescription() . '",
              "basket": ' .  $basket . ',
              "payment_methods": ' . json_encode($payment_methods) . ',
              "tsp_id":18,
              "return_url": "' . $this->notify_url . '",
              "notify_url": "' . $this->return_url . '",
              "language": ' . config('octo.locale', 'en') . ',
              "ttl": 15
            }';
        return $request;
    }

    private function createOrder()
    {

        $transaction = OctoTransactions::where('shop_transaction_id', $this->order->id)->first();
        if(!$transaction)
            $transaction = new OctoTransactions();
        $transaction->user_id = $this->user->id;
        $transaction->shop_transaction_id = $this->order->id;
        $transaction->booking_id = $this->order->id;

        $transaction->price = $this->order_price;
        $transaction->octo_payment_UUID = $this->payment_uuid;
        $transaction->status = $this->response->status;
        $transaction->octo_pay_url = $this->response->octo_pay_url;
        $transaction->currency = "UZS";
        $transaction->save();
        return 0;
    }

    private function getDescription()
    {
        return "Payment " . $this->order->id;
    }
}
