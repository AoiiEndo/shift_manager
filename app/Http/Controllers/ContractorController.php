<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Availability;
use App\Models\Shift;
use App\Models\User;
use App\Enums\Authority;
use Carbon\Carbon;

class ContractorController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            if (Auth::user()->authorities != Authority::Contractor->value) {
                abort(403, 'このページにアクセスする権限がありません。');
            }
            return $next($request);
        });
    }

    public function index()
    {
        $organization_id = Auth::user()->organization_id;

        $availabilities = Availability::whereHas('user', function($query) use ($organization_id) {
            $query->where('organization_id', $organization_id);
        })
        ->with('user')
        ->get()
        ->map(function ($availability) {
            $availability->start_time = Carbon::parse($availability->start_time)->format('H:i');
            $availability->end_time = Carbon::parse($availability->end_time)->format('H:i');
            return $availability;
        });

        $shifts = Shift::whereHas('user', function($query) use ($organization_id) {
            $query->where('organization_id', $organization_id);
        })
        ->with('user')
        ->get()
        ->map(function ($shift) {
            $shift->start_time = Carbon::parse($shift->start_time)->format('H:i');
            $shift->end_time = Carbon::parse($shift->end_time)->format('H:i');
            return $shift;
        });

        return view('contractor.index', ['availabilities' => $availabilities, 'shifts' => $shifts]);
    }

    public function create()
    {
        $organization_id = Auth::user()->organization_id;
        $users = User::where('organization_id', $organization_id)
                ->where('id', '!=', Auth::id())
                ->get();
        
        return view('contractor.create', compact('users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
        ]);

        $password = Str::random(8);;

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($password),
            'authorities' => Authority::Employee->value,
            'organization_id' => Auth::user()->organization_id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Mail::to($employee->email)->send(new UserCreated($employee, $password));

        return redirect()->route('contractor.create')->with('success', '新規ユーザを作成し、作成ユーザにメールでお知らせしました。パスワード:'.$password);
    }

    public function destroy(Employee $employee)
    {
        $employee->delete();

        return redirect()->route('contractor.create')->with('success', '被雇用者が削除されました。');
    }
}
