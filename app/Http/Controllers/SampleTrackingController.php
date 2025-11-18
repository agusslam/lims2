<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SampleTrackingController extends Controller
{
    public function index()
    {
        // logika tracking
        return view('tracking.index');
    }
}
