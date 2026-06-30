<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class WattVisionController extends Controller
{
    public function dashboard()
    {
        return view('wattvision.dashboard');
    }

    public function login()
    {
        return view('wattvision.login');
    }
}
