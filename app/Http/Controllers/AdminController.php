<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Organization;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Enums\Authority;
use Illuminate\Support\Facades\Mail;
use App\Mail\UserCreated;
use Illuminate\Support\Str;

class AdminController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            if (Auth::user()->authorities != Authority::Admin->value) {
                abort(403, 'このページにアクセスする権限がありません。');
            }
            return $next($request);
        });
    }

    public function index()
    {
        $users = User::where('authorities', Authority::Contractor)->get();
        return view('admin.index', compact('users'));
    }

    public function create()
    {
        return view('admin.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'oganization' => 'required|string|max:255',
        ]);

        $Organization = Organization::create([
            'name' => $request->oganization,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $password = Str::random(8);
        $hashedPassword = Hash::make($password);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => $hashedPassword,
            'authorities' => Authority::Contractor->value,
            'organization_id' => $Organization->id,
        ]);

        // Mail::to($user->email)->send(new UserCreated($user, $password));

        return redirect()->route('admin.index')
            ->with('success', 'ユーザが作成されました。仮パスワードは: ' . $password);
    }
}
