<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\User;

class UserController extends Controller
{
    /**
     * Get the authenticated User Profile
     *
     * @return [json] user object
     */
    public function profile(Request $request)
    {
        return response()->json($request->user());
    }
}