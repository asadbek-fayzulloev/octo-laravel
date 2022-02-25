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
        $this->octoService = new OctoService(config('url.redirect_after_verify'));

    }
    public function pay(Order $order, $type, OctoRequest $request){
        $this->octoService->setNotifyUrl(config('octo.url.notify_url').$order->id);
        $this->octoService->setReturnUrl(config('octo.url.return_url').$order->id);

        $this->octoService->setDetails($order->id);
        $this->octoService->setType($type);
        $this->octoService->setNotifyUrl(route('octo.notify', ['order' => $order->id, 'type' => $type]));
        $this->octoService->setReturnUrl(config('url.return_url'));
        $url = $this->octoService->pay();
        return redirect($url);

    }
    public function verify(Order $order, $type, OctoRequest $request){
        $this->octoService->setNotifyUrl(config('octo.url.notify_url').$order->id);
        $this->octoService->setReturnUrl(config('octo.url.return_url').$order->id);

        $this->octoService->setDetails($order->id);
        $this->octoService->setType($type);
        $this->octoService->setNotifyUrl(route('octo.notify',['order' => $order->id, 'type' => $type]));
        $this->octoService->setReturnUrl(config('url.return_url'));
        $url = $this->octoService->verify();
        return redirect($url);
    }
    public function notify(Order $order, $type, OctoRequest $request){
        $this->octoService->setNotifyUrl(config('octo.url.notify_url').$order->id);
        $this->octoService->setReturnUrl(config('octo.url.return_url').$order->id);

        $this->octoService->setDetails($order->id);
        $this->octoService->setType($type);
        $url = $this->octoService->notify();
        return redirect($url);
    }
}
