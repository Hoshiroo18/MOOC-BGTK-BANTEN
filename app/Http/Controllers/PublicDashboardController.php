<?php

namespace App\Http\Controllers;

use App\Models\Kegiatan;

class PublicDashboardController extends Controller
{
    public function index()
    {
        $kegiatan = Kegiatan::latest()->get();

        return view('dashboard', compact('kegiatan'));
    }
}