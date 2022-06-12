<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TokenStatus extends Model
{
    use HasFactory;

    protected $table = 'token_status';
    public $timestamps = false;
    /**
     * Get the notes for the status.
     */
    public function tokens()
    {
        return $this->hasMany('App\Models\Tokens');
    }
}
