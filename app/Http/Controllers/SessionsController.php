<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use SebastianBergmann\CodeCoverage\Report\Html\Dashboard;

class SessionsController extends Controller
{
    public function create()
    {
        return view('session.login-session');
    }

    public function store()
    {
        // VALIDATE
        $attributes = request()->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        // LOGIN
        if(Auth::attempt($attributes))
        {
            // REGENERATE SESSION
            session()->regenerate();

            // REDIRECT & FLASH
            return redirect('dashboard')->with([
                'success' => 'You are logged in.'
            ]);
        } 
        else {
            // REDIRECT & FLASH
            return back()->withErrors([
                'email' => 'Email or password invalid.'
            ]);
        }

    }
    
    public function destroy()
    {

        Auth::logout();

        session()->invalidate();
        session()->regenerateToken();

        return redirect('/login')->with(['success'=>'You\'ve been logged out.']);
    }
}
