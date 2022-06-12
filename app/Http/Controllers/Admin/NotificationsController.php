<?php

namespace App\Http\Controllers\admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Notifications;
use App\Models\Status;
use App\Http\Controllers\Controller;

class NotificationsController extends Controller
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
        $notifications = Notifications::all();
        return view('admin.notifications.notificationsList', ['notifications' => $notifications]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $statuses = Status::all();
        return view('admin.notifications.create', [ 'statuses' => $statuses ]);
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
            'content'           => 'required',
            'status_id'         => 'required',
            'applies_to_date'   => 'required|date_format:Y-m-d',
        ]);
        $user = auth()->user();
        $notification = new Notifications();
        $notification->content   = $request->input('content');
        $notification->status_id = $request->input('status_id');
        $notification->applies_to_date = $request->input('applies_to_date');
        $notification->users_id = $user->id;
        $notification->save();
        $request->session()->flash('message', 'Successfully created notification');
        return redirect()->route('notifications.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $notification = Notifications::with('user')->with('status')->find($id);
        return view('admin.notifications.notificationShow', [ 'notification' => $notification ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $notification = Notifications::find($id);
        $statuses = Status::all();
        return view('admin.notifications.edit', [ 'statuses' => $statuses, 'notification' => $notification ]);
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
            'content'           => 'required',
            'status_id'         => 'required',
            'applies_to_date'   => 'required|date_format:Y-m-d',
        ]);
        $notification = Notifications::find($id);
        $notification->content   = $request->input('content');
        $notification->status_id = $request->input('status_id');
        $notification->applies_to_date = $request->input('applies_to_date');
        $notification->save();
        $request->session()->flash('message', 'Successfully edited notification');
        return redirect()->route('notifications.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $notification = Notifications::find($id);
        if($notification){
            $notification->delete();
        }
        return redirect()->route('notifications.index');
    }
}
