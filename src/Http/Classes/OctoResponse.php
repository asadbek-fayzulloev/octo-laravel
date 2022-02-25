<?php

namespace Asadbek\OctoLaravel\Http\Classes;

use Illuminate\Support\Arr;

class OctoResponse
{
    public $error;
    public $status;
    public $shop_transaction_id;
    public $octo_payment_UUID;
    public $octo_pay_url;
    public function __construct(array $response){
        $this->error = $response["error"];
        $this->status = Arr::get($response, "status");
        $this->shop_transaction_id = Arr::get($response, "shop_transaction_id");
        $this->octo_payment_UUID = Arr::get($response, "octo_payment_UUID");
        $this->octo_pay_url = Arr::get($response, "octo_pay_url");
    }
}