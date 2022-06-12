<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payorders extends Model
{
    use HasFactory;

    protected $table = 'payorders';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'payment_id', 'unit', 'amount', 'payment_type', 'reference','transaction_time'
    ];

    /**
     * belongs to user id
     */
    public function user()
    {
        return $this->belongsTo('App\Models\Users', 'user_id')->withTrashed();
    }
}
