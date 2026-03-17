<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Referral extends Model
{
    use HasFactory;

    protected $connection = 'mysql';
    protected $table = 'referrals';

    protected $fillable = [
        'firebase_key',
        'patient',
        'gender',
        'age',
        'diagnosis',
        'chief_complaint',
        'ward',
        'bed_no',
        'department'
    ];

    public $timestamps = false;

}
