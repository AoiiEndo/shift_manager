<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Enums\Authority;
use App\Models\Availability;
use App\Models\Shift;
use App\Rules\ShiftNotConfirmed;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class EmployeeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            if (Auth::user()->authorities != Authority::Employee->value) {
                abort(403, 'このページにアクセスする権限がありません。');
            }
            return $next($request);
        });
    }

    public function index()
    {
        $organization_id = Auth::user()->organization_id;
        $user_id = Auth::user()->id;
        $availabilities = Availability::where('organization_id', $organization_id)
                            ->where('user_id', $user_id)
                            ->get()
                            ->map(function ($availability) {
                                $availability->start_time = Carbon::parse($availability->start_time)->format('H:i');
                                $availability->end_time = Carbon::parse($availability->end_time)->format('H:i');
                                return $availability;
                            });
        $shifts = Shift::where('organization_id', $organization_id)
                    ->where('user_id', $user_id)
                    ->get()
                    ->map(function ($shift) {
                        $shift->start_time = Carbon::parse($shift->start_time)->format('H:i');
                        $shift->end_time = Carbon::parse($shift->end_time)->format('H:i');
                        return $shift;
                    });

        return view('employee.index', ['availabilities' => $availabilities, 'shifts' => $shifts]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'start_datetime' => 'required|date_format:Y-m-d\TH:i',
            'end_datetime' => 'required|date_format:Y-m-d\TH:i',
            'date' => 'required|date',
        ]);

        $$availability = new Availability([
            'user_id' => Auth::user()->id,
            'start_time' => $request->get('start_datetime'),
            'end_time' => $request->get('end_datetime'),
            'date' => $request->get('date'),
            'organization_id' => Auth::user()->organization_id,
        ]);

        $$availability->save();

        $$availabilities = Availability::where('user_id', Auth::user()->id)->get();

        return redirect()->back()->with('availabilities', $availabilities);
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'start_datetime' => 'required|date_format:Y-m-d\TH:i',
            'end_datetime' => 'required|date_format:Y-m-d\TH:i',
            'date' => 'required|date',
            'id' => ['required',
                    'exists:availabilities,id',
                    new ShiftNotConfirmed($request->get('id')),
            ],
        ]);

        $shift = Availability::find($request->get('id'));
        $shift->start_time = $request->get('start_datetime');
        $shift->end_time = $request->get('end_datetime');
        $shift->date = $request->get('date');
        $shift->save();

        $organization_id = Auth::user()->organization_id;
        $user_id = Auth::user()->id;
        $availabilities = Availability::where('organization_id', $organization_id)
                            ->where('user_id', $user_id)
                            ->get()
                            ->map(function ($availability) {
                                $availability->start_time = Carbon::parse($availability->start_time)->format('H:i');
                                $availability->end_time = Carbon::parse($availability->end_time)->format('H:i');
                                return $availability;
                            });
        $shifts = Shift::where('organization_id', $organization_id)
                    ->where('user_id', $user_id)
                    ->get()
                    ->map(function ($shift) {
                        $shift->start_time = Carbon::parse($shift->start_time)->format('H:i');
                        $shift->end_time = Carbon::parse($shift->end_time)->format('H:i');
                        return $shift;
                    });

        return response()->json([
            'exists' => true,
            'success' => 'シフト希望が正常に更新されました。',
            'availabilities' => $availabilities,
            'shifts' => $shifts
        ]);
    }

    public function checkShift(Request $request)
    {
        $date = $request->input('date');
        $availability = Availability::where('date', $date)
                        ->where('user_id', auth()->id())
                        ->first();

        if ($availability) {
            return response()->json([
                'exists' => true,
                'availability' => [
                    'id' => $availability->id,
                    'start_time' => Carbon::parse($availability->start_time)->format('H:i'),
                    'end_time' => Carbon::parse($availability->end_time)->format('H:i'),
                ]
            ]);
        } else {
            return response()->json([
                'exists' => false
            ]);
        }
    }

    public function delete(Request $request)
    {
        $validated = $request->validate([
            'id' => 'required|exists:availabilities,id',
        ]);

        $availability = Availability::find($request->get('id'));
        $availability->delete();

        return response()->json([
            'success' => 'シフトが正常に削除されました。'
        ]);
    }
}
