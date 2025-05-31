<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LandingPageController extends Controller
{
    // App/Http/Controllers/LandingPageController.php
    public function index()
    {
        // Jika user sudah login, redirect ke dashboard
        if (auth()->check()) {
            return redirect()->route('dashboard');
        }
        
        // Jika belum login, tampilkan landing page
        return view('landingpage'); // atau view yang sesuai
    }
}
