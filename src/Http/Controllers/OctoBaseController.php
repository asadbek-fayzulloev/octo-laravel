<?php

namespace Asadbek\OctoLaravel\Http\Controllers;

use App\Http\Controllers\Controller;
use Asadbek\OctoLaravel\Models\OctoTransactions;
use Asadbek\OctoLaravel\Models\Order;
use Asadbek\OctoLaravel\Models\User;
use Asadbek\OctoLaravel\Requests\OctoRequest;
use Asadbek\OctoLaravel\Services\OctoService;
use Illuminate\Http\Request;

class OctoBaseController extends Controller
{
    private OctoService $octoService;
    public function __construct(){
        $notify_url = "www.google.com";
        $return_url = 'www.google.com';
        $this->octoService = new OctoService($notify_url, $return_url, config('redirect_after_verify'));
    }
    public function pay(Order $order, OctoRequest $request){
        $this->octoService->setDetails($order->id);
        $url = $this->octoService->pay($request->type);
        return redirect()->route($url);

    }
    public function verify($order_id, OctoRequest $request){
        $this->octoService->setDetails($order_id);
    }
}
