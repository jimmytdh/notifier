<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Log extends Model
{
    use HasFactory;

    protected $connection = 'mysql';
    protected $table = 'logs';

    protected $fillable = [
        'referral_id',
        'forwarded_by',
        'origin',
        'destination',
        'remarks',
    ];

    public $timestamps = false;
}
