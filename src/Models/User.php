<?php

namespace Asadbek\OctoLaravel\Models;

use Illuminate\Database\Eloquent\Model;
use JsonSerializable;

class User extends Model implements JsonSerializable
{
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->table = config('octo.table.users');

    }
    public function jsonSerialize()
    {
        return
            [
                "user_id" => $this->id,
                "phone" => $this->phone,
                "email" => $this->email,
            ];
    }
    protected $fillable = [
        'name',
    ];
}
