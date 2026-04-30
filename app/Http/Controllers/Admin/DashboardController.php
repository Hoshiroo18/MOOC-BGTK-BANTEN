<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $totalCourses = Course::count();
        $publishedCourses = Course::where('status', 'published')->count();
        $draftCourses = Course::where('status', 'draft')->count();

        $totalUsers = User::count();

        $latestCourses = Course::latest()->take(6)->get();

        return view('admin.dashboard', compact(
            'totalCourses',
            'publishedCourses',
            'draftCourses',
            'totalUsers',
            'latestCourses'
        ));
    }

    public function storeCourse(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'category' => 'nullable|string|max:100',
            'level' => 'nullable|string|max:100',
            'duration_hours' => 'nullable|integer|min:0',
            'status' => 'required|in:draft,published',
            'description' => 'nullable|string',
        ]);

        Course::create($validated);

        return redirect()
            ->route('admin.dashboard')
            ->with('success', 'Kelas berhasil ditambahkan ke database.');
    }
}