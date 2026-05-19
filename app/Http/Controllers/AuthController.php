<?php

namespace App\Http\Controllers;

use App\Models\RoleMapping;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthController extends Controller
{
    public function index()
    {
        return view('auth.login', [
            'title' => 'Login'
        ]);
    }

    public function welcome()
    {
        return view('login.welcome', [
            'title' => 'welcome'
        ]);
    }

    public function authenticate(Request $request)
    {
        $request->validate([
            'email'    => ['required'],
            'password' => ['required'],
        ]);

        $login    = $request->input('email');
        $password = $request->input('password');

        $user = User::where('email', $login)
            ->orWhere('user_name', $login)
            ->first();

        if ($user && Hash::check($password, $user->password)) {
            Auth::login($user, $request->boolean('remember'));
            $request->session()->regenerate();

            $role = DB::table('role')
                ->where('role_id', $user->role_id)
                ->first();

            session(['roles' => $role]);

            // Redirect otomatis berdasarkan role_id
            switch ($user->role_id) {
                case 1: // Administrator
                    return redirect()->route('admin.dashboard');
                case 2: // Supervisor
                    return redirect()->route('admin.dashboard');
                default: // Guru, Peserta, dll
                    return redirect()->route('dashboard');
            }
        }

        return back()->withErrors([
            'email' => 'Email/username atau password salah.',
        ])->onlyInput('email');
    }

    public function changeRole(Request $request)
    {
        $request->validate([
            'ddl_role' => 'required'
        ]);

        $role_id = $request->ddl_role;
        $user    = Auth::user();

        DB::table('users')
            ->where('user_id', $user->user_id)
            ->update(['role_id' => $role_id]);

        session(['role_aktif' => $role_id]);

        return redirect()->back();
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }
}
