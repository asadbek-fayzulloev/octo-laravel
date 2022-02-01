<?php

namespace Asadbek\OctoLaravel\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use JsonSerializable;

class Order extends Model implements JsonSerializable
{
    use HasFactory;
    protected $table = 'bookings';

    protected $fillable = [
        'name',
    ];
    public function jsonSerialize()
    {
        return
            [
                "user_id" => $this->id,
                "phone" => $this->phone,
                "email" => $this->email
            ];
    }
}
