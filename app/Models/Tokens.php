<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tokens extends Model
{
    use HasFactory;

    protected $table = 'tokens';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'token_name',
        'token_symbol',
        'token_decimal',
        'token_logo',
        'token_pair_type',
        'token_whitepaper',
        'for_cefi',
        'wallet_address',
        'withdraw_fee',
        'deposit_fee',
        'transfer_fee',
        'is_deleted',
        'status',
    ];

    /**
     * Get the Status that owns the Notes.
     */
    public function token_status()
    {
        return $this->belongsTo('App\Models\TokenStatus', 'status');
    }
}
