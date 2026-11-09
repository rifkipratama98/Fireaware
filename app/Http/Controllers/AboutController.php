<?php

namespace App\Http\Controllers;

use App\Models\Province;
use Illuminate\Contracts\View\View;

class AboutController extends Controller
{
    /**
     * Halaman publik: Tentang FireAware Kalimantan.
     */
    public function index(): View
    {
        $provinces = Province::query()->orderBy('name')->get();

        return view('tentang', [
            'provinces' => $provinces,
        ]);
    }
}
