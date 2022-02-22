<?php

namespace Asadbek\OctoLaravel\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    use HasFactory;
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->table = config('octo.table.users');

    }
    protected $fillable = [
        'name',
    ];
}
