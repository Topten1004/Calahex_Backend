<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class News extends Model
{

    use HasFactory;

    protected $table = 'news';

    /**
     * Get the User that owns the Notes.
     */
    public function user()
    {
        return $this->belongsTo('App\Models\User', 'users_id');
    }

    /**
     * Get the Status that owns the News.
     */
    public function status()
    {
        return $this->belongsTo('App\Models\Status', 'status_id');
    }

    /**
     * Get the Titles that owns the News.
     */
    public function news_title()
    {
        return $this->belongsTo('App\Models\NewsTitles', 'title');
    }
}
