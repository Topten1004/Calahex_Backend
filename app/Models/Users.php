<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Class Users for admin panel managers
 * @package App\Models
 */

class Users extends Model
{
    use SoftDeletes;
    use HasFactory;

    /**
     * Get the notes for the users.
     */
    public function notes()
    {
        return $this->hasMany('App\Models\Notes');
    }

    protected $dates = [
        'deleted_at'
    ];
}
