<?php

namespace Asadbek\OctoLaravel\Http\Controllers;

use App\Http\Controllers\Controller;
use Asadbek\OctoLaravel\Models\OctoTransactions;
use Asadbek\OctoLaravel\Models\Order;
use Asadbek\OctoLaravel\Models\User;
use Asadbek\OctoLaravel\Requests\OctoRequest;
use Illuminate\Http\Request;

class OctoBaseController extends Controller
{
    public function pay($shop_transaction_id, OctoRequest $request){
//        $query = 'SELECT * FROM `octo_transactions` WHERE shop_transaction_id = ' . $shop_transaction_id;
//        $statement = $this->conn->prepare($query);
//        $statement->execute();
//        $result = $statement->setFetchMode(\PDO::FETCH_ASSOC);
//        return $statement->fetch();
        OctoTransactions::where('shop_transaction_id', $shop_transaction_id)->first();
    }
    public function verify(OctoRequest $request){

    }
}
