<?php

namespace App\Http\Controllers\admin;

use App\Models\NewsTitles;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\News;
use App\Models\Status;
use App\Http\Controllers\Controller;

class NewsController extends Controller
{

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $news = News::all();
        return view('admin.news.newsList', ['newses' => $news]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $statuses = Status::all();
        $titles = NewsTitles::all();
        return view('admin.news.create', [ 'statuses' => $statuses, 'titles' => $titles ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'title'             => 'required|min:1|max:64',
            'content'           => 'required',
            'status_id'         => 'required',
            'applies_to_date'   => 'required|date_format:Y-m-d',
        ]);
        $user = auth()->user();
        $news = new News();
        $news->title     = $request->input('title');
        $news->content   = $request->input('content');
        $news->status_id = $request->input('status_id');
        $news->applies_to_date = $request->input('applies_to_date');
        $news->users_id = $user->id;
        $news->save();
        $request->session()->flash('message', 'Successfully created news');
        return redirect()->route('news.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $news = News::with('user')->with('status')->find($id);
        return view('admin.news.newsShow', [ 'news' => $news ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $news = News::find($id);
        $statuses = Status::all();
        $titles = NewsTitles::all();
        return view('admin.news.edit', [ 'statuses' => $statuses, 'news' => $news, 'titles' => $titles ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //var_dump('bazinga');
        //die();
        $validatedData = $request->validate([
            'title'             => 'required|min:1|max:64',
            'content'           => 'required',
            'status_id'         => 'required',
            'applies_to_date'   => 'required|date_format:Y-m-d',
        ]);
        $news = News::find($id);
        $news->title     = $request->input('title');
        $news->content   = $request->input('content');
        $news->status_id = $request->input('status_id');
        $news->applies_to_date = $request->input('applies_to_date');
        $news->save();
        $request->session()->flash('message', 'Successfully edited news');
        return redirect()->route('news.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $news = News::find($id);
        if($news){
            $news->delete();
        }
        return redirect()->route('news.index');
    }
}
