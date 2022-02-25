<?php

namespace Asadbek\OctoLaravel\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OctoTransactions extends Model
{
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->table = config('octo.table.transactions');

    }
    protected $fillable = [
        'name',
        'shop_transaction_id',
        'user_id',
        'booking_id',
        'price',
        'currency',
        'octo_payment_UUID',
        'status'
    ];
    public function user(){
        return $this->belongsTo(User::class);
    }
    public function order(){
        return $this->belongsTo(Order::class);
    }
}
