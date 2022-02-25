<?php

namespace Asadbek\OctoLaravel\Models;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->table = config('octo.table.users');

    }
    protected $fillable = [
        'name',
    ];
}
