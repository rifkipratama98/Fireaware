<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Menampilkan dashboard admin.
     */
    public function index(): View
    {
        return view('admin.dashboard');
    }
}
