<?php

namespace Asadbek\OctoLaravel\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OctoTransactions extends Model
{
    use HasFactory;
    protected $table = 'octo_transactions';

    protected $fillable = [
        'name',
    ];
}
