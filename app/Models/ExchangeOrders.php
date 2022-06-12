<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExchangeOrders extends Model
{
    use HasFactory;

    protected $table = 'exchange_orders';
    protected $fillable = [
        'user_id', 'type', 'limit_price', 'amount', 'total', 'unit_from', 'unit_to'
    ];
    /**
     * Get the token info
     */
    public function token()
    {
        return $this->belongsTo('App\Models\Tokens', 'unit_from');
    }
}
