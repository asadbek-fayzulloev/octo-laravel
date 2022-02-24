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
    public function pay(Order $order, OctoRequest $request){
        $this->octoService->setDetails($order->id);
        $this->octoService->setType($request->type);
        $this->octoService->setNotifyUrl(route('octo.notify'));
        $this->octoService->setReturnUrl(config('url.return_url'));
        $url = $this->octoService->pay();
        return redirect()->route($url);

    }
    public function verify(Order $order, OctoRequest $request){
        $this->octoService->setDetails($order->id);
        $this->octoService->setType($request->type);
        $this->octoService->setNotifyUrl(route('octo.notify'));
        $this->octoService->setReturnUrl(config('url.return_url'));
        $url = $this->octoService->verify();
        return redirect()->route($url);
    }
    public function notify(Order $order, OctoRequest $request){
        $this->octoService->setDetails($order->id);
        $this->octoService->setType($request->type);
        $url = $this->octoService->notify();
        return redirect()->route($url);
    }
}
