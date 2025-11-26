<?php

namespace App\Http\Controllers;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        if ($user->role == 'Admin') {
            //
        } else {
        //        
        }

    
        return view('pages.dashboard');
    }

}
