<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\News;
use App\Models\NewsTitles;
use App\Models\Status;
use Illuminate\Http\Request;

class NewsTitlesController extends Controller
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
        $newsTitles = NewsTitles::all();
        return view('admin.newsTitles.newsTitlesList', ['newsTitles' => $newsTitles]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.newsTitles.create');
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
            'name'             => 'required|min:1|max:64',
        ]);
        $news = new NewsTitles();
        $news->name     = $request->input('name');
        $news->save();
        $request->session()->flash('message', 'Successfully created news');
        return redirect()->route('news_titles.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $newsTitle = NewsTitles::find($id);
        return view('admin.newsTitles.newsTitleShow', [ 'newsTitle' => $newsTitle ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $newsTitle = NewsTitles::find($id);
        return view('admin.newsTitles.edit', [ 'newsTitle' => $newsTitle ]);
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
            'name'             => 'required|min:1|max:64',
        ]);
        $news = NewsTitles::find($id);
        $news->name     = $request->input('name');
        $news->save();
        $request->session()->flash('message', 'Successfully edited news');
        return redirect()->route('news_titles.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $news = NewsTitles::find($id);
        if($news){
            $news->delete();
        }
        return redirect()->route('news_titles.index');
    }
}
