<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Enums\Authority;

class LoginController extends Controller
{

    public function showLoginForm()
    {
        return view('login/index');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($request->only('email', 'password'))) {
            $request->session()->regenerate();

            return $this->authenticated($request, Auth::user());
        }

        return back()->withErrors([
            'email' => 'メールアドレスまたはパスワードが正しくありません。',
        ]);
    }

    protected function authenticated(Request $request, $user)
    {
        switch ($user->authorities) {
            case Authority::Admin->value:
                return redirect()->intended('/admin/users');
            case Authority::Contractor->value:
                return redirect()->intended('/contractor/index');
            case Authority::Employee->value:
                return redirect()->intended('/employee/index');
            default:
                return redirect()->intended('/login');
        }
    }

    public function logout()
    {
        Auth::logout();
        session()->invalidate();
        session()->regenerateToken();
        return redirect('/')->with('status', 'ログアウトしました。');
    }
}
