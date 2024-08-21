<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Availability;
use App\Models\Shift;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Log;

class ShiftController extends Controller
{
    public function getAvailabilitiesByDate($date)
    {
        $organization_id = Auth::user()->organization_id;
        $availabilities = Availability::whereHas('user', function($query) use ($organization_id) {
            $query->where('organization_id', $organization_id);
        })
        ->with('user')
        ->where('date', $date)
        ->get()
        ->map(function ($availability) {
            $availability->start_time = Carbon::parse($availability->start_time)->format('H:i');
            $availability->end_time = Carbon::parse($availability->end_time)->format('H:i');
            return $availability;
        });

        return response()->json(['availabilities' => $availabilities]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i',
            'date' => 'required|date',
        ]);

        $availability = Availability::where('user_id', $request->user_id)
                        ->where('date', $request->date)
                        ->first();

        if (!$availability) {
            return response()->json(['success' => false, 'message' => 'シフト希望が見つかりません。'], 422);
        }

        $availabilityStart = Carbon::parse($availability->start_time)->format('H:i');
        $availabilityEnd = Carbon::parse($availability->end_time)->format('H:i');
        $shiftStart = Carbon::parse($request->start_time)->format('H:i');
        $shiftEnd = Carbon::parse($request->end_time)->format('H:i');

        if ($shiftStart < $availabilityStart || $shiftEnd > $availabilityEnd) {
            return response()->json(['success' => false, 'message' => 'シフト確定の時間がシフト希望の範囲を超えています。'], 422);
        }

        $shift = new Shift();
        $shift->user_id = $request->user_id;
        $shift->start_time = Carbon::parse($request->start_time)->format('H:i');
        $shift->end_time = Carbon::parse($request->end_time)->format('H:i');
        $shift->date = $request->date;
        $shift->organization_id = Auth::user()->organization_id;
        $shift->availability_id = $availability->id;
        $shift->save();

        return response()->json(['success' => true]);
    }
}
