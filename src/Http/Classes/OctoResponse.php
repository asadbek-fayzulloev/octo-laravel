<?php

namespace Asadbek\OctoLaravel\Http\Classes;

class OctoResponse
{
    public $error;
    public $status;
    public $shop_transaction_id;
    public $octo_payment_UUID;
    public $octo_pay_url;
    public function __construct(array $response){
        $this->error = $response["error"];
        $this->status = $response["status"];
        $this->shop_transaction_id = $response["shop_transaction_id"];
        $this->octo_payment_UUID = $response["octo_payment_UUID"];
        $this->octo_pay_url = $response["octo_pay_url"];
    }
}