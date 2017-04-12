<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class HomeController extends Controller
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
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return view('home', [
            'user' => $request->user()
        ]);
    }

    public function search(Request $request)
    {
        $location = $request->get('location', '');
        $users = User::where('location', $location)->get();

        return \response()->json($users);
    }

    public function locationAutocomplete(Request $request)
    {
        $term = $request->get('term', '');
        $users = User::select('location')->distinct()
                                         ->where('location', 'LIKE', "%{$term}%")
                                         ->get();
        $locations = [];
        foreach ($users as $user) {
            $locations[] = $user->location;
        }
        return \response()->json($locations);
    }
}
