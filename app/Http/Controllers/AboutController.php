<?php

namespace App\Http\Controllers;

use App\Models\Province;
use Inertia\Inertia;
use Inertia\Response;

class AboutController extends Controller
{
    /**
     * Halaman publik: Tentang FireAware Kalimantan.
     */
    public function index(): Response
    {
        $provinces = Province::query()->orderBy('name')->get();

        return Inertia::render('Public/About', [
            'provinces' => $provinces,
        ]);
    }
}
