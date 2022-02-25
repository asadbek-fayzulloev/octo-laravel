<?php

namespace Asadbek\OctoLaravel\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use JsonSerializable;

class Order extends Model implements JsonSerializable
{
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->table = config('octo.table.orders');

    }

    protected $fillable = [
        'name',
    ];
    public function jsonSerialize()
    {
        return
            [
                "position_desc" => 'Order '. $this->id,
                "count" => 1,
                "price" => $this->uzPrice,
                "supplier_shop_id" => config('octo.octo_shop_id')
            ];
    }
    public function user(){
        $this->belongsTo(User::class, 'user_id', 'id');
    }

}
