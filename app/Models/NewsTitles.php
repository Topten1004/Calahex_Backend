<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NewsTitles extends Model
{
    use HasFactory;

    protected $table = 'news_titles';
    public $timestamps = false;
    /**
     * Get the notes for the status.
     */
    public function news()
    {
        return $this->hasMany('App\Models\News');
    }
}
