<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Accounts extends Model
{
    use HasFactory;

    protected $table = 'accounts';
    /**
     * Get the token info
     */
    public function token()
    {
        return $this->belongsTo('App\Models\Tokens', 'token_id');
    }
    /**
     * belongs to wallet id
     */
    public function wallet()
    {
        return $this->belongsTo('App\Models\Wallets', 'wallet_id');
    }
}
