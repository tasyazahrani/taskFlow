<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    // HAPUS __construct() dan middleware dari sini
    // Karena sudah ada middleware di routes
    
    public function index()
    {
        return view('dashboard');
    }
}