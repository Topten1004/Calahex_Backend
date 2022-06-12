<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\News;
use App\Models\Notifications;

class SupportController extends Controller
{
    /**
     * Get ongoing news list
     *
     * @return [json] news list
     */
    public function news(Request $request)
    {
        $news_list = News::whereHas('status', function($status){
            $status->where('name', 'ongoing');
        })->get();

        $result = [];

        foreach($news_list as $news){
            $result[$news->news_title['name']][] = $news['content'];
        }
        return response()->json($result, 201);
    }
    /**
     * Get ongoing notification list
     *
     * @return [json] notification list
     */
    public function notification(Request $request)
    {
        $notification_list = Notifications::whereHas('status', function($status){
            $status->where('name', 'ongoing');
        })->get();

        $result = [];

        foreach($notification_list as $notification){
            $result[] = $notification['content'];
        }
        return response()->json($result, 201);
    }
}
