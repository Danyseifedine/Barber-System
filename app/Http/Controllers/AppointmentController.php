<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Appointment;
use App\Models\Service;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class AppointmentController extends BaseController
{
    /**
     * Store a newly created appointment in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Validate the request
        $request->validate([
            'services' => 'required|array',
            'services.*' => 'exists:services,id',
            'appointment_date' => 'required|date|after_or_equal:today',
            'appointment_time' => 'required',
        ]);

        // Parse appointment date and time
        $appointmentDate = $request->appointment_date;
        $appointmentTime = $request->appointment_time;

        // Calculate end time based on service duration
        $startTime = Carbon::parse($appointmentTime);
        $endTime = clone $startTime;

        // Get total duration of selected services
        $totalDuration = 0;
        foreach ($request->services as $serviceId) {
            $service = Service::find($serviceId);
            $totalDuration += $service->duration_minutes;
        }

        // Add duration to end time
        $endTime->addMinutes($totalDuration);

        // Check if the appointment time is available
        $conflictingAppointments = Appointment::where('appointment_date', $appointmentDate)
            ->where(function ($query) use ($startTime, $endTime) {
                $query->whereBetween('start_time', [$startTime->format('H:i'), $endTime->format('H:i')])
                    ->orWhereBetween('end_time', [$startTime->format('H:i'), $endTime->format('H:i')])
                    ->orWhere(function ($q) use ($startTime, $endTime) {
                        $q->where('start_time', '<', $startTime->format('H:i'))
                            ->where('end_time', '>', $endTime->format('H:i'));
                    });
            })
            ->count();

        if ($conflictingAppointments > 0) {
            return redirect()->back()->with('error', 'The selected time slot is not available. Please choose another time.');
        }

        // Create the appointment
        $appointment = new Appointment();
        $appointment->user_id = Auth::id();
        $appointment->appointment_date = $appointmentDate;
        $appointment->start_time = $startTime->format('H:i');
        $appointment->end_time = $endTime->format('H:i');
        $appointment->status = 'scheduled';
        $appointment->notes = $request->notes;
        $appointment->save();

        // Attach services to the appointment
        foreach ($request->services as $serviceId) {
            $appointment->services()->attach($serviceId);
        }

        return redirect()->route('appointments.confirmation', $appointment->id)
            ->with('success', 'Your appointment has been booked successfully!');
    }

    /**
     * Display appointment confirmation page.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function confirmation($id)
    {
        $appointment = Appointment::with('services')->findOrFail($id);

        // Check if the appointment belongs to the authenticated user
        if ($appointment->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        return view('appointments.confirmation', compact('appointment'));
    }

    /**
     * Display a listing of the user's appointments.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $appointments = Appointment::with('services')
            ->where('user_id', Auth::id())
            ->orderBy('appointment_date', 'asc')
            ->orderBy('start_time', 'asc')
            ->get();

        return view('appointments.index', compact('appointments'));
    }

    /**
     * Cancel an appointment.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function cancel($id)
    {
        $appointment = Appointment::findOrFail($id);

        // Check if the appointment belongs to the authenticated user
        if ($appointment->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        // Get appointment date as Carbon instance
        $appointmentDate = Carbon::parse($appointment->appointment_date);
        $today = Carbon::today();

        // If appointment date is in the past, prevent cancellation
        if ($appointmentDate->lt($today)) {
            return redirect()->back()->with('error', 'You cannot cancel a past appointment.');
        }

        // If appointment is today, check the time
        if ($appointmentDate->isToday()) {
            $appointmentDateTime = Carbon::parse($appointment->appointment_date . ' ' . $appointment->start_time);
            $now = Carbon::now();

            // Calculate time until appointment
            $hoursUntilAppointment = $now->diffInHours($appointmentDateTime, false);

            // Only prevent cancellation if less than 1 hour before appointment
            if ($hoursUntilAppointment < 1 && $hoursUntilAppointment >= 0) {
                return redirect()->back()->with('error', 'Appointments cannot be cancelled less than 1 hour before the scheduled time.');
            }
        }

        // Update appointment status
        $appointment->status = 'cancelled';
        $appointment->save();

        return redirect()->back()->with('success', 'Your appointment has been cancelled successfully.');
    }

    /**
     * Check if an appointment time is available.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function checkAvailability(Request $request)
    {
        // Validate the request
        $request->validate([
            'date' => 'required|date|after_or_equal:today',
            'time' => 'required',
            'duration' => 'required|integer|min:1',
        ]);

        // Parse appointment date and time
        $appointmentDate = $request->date;
        $startTime = $request->time;
        $duration = $request->duration;

        // Calculate end time based on service duration
        $startDateTime = Carbon::parse($appointmentDate . ' ' . $startTime);
        $endDateTime = (clone $startDateTime)->addMinutes($duration);

        $endTime = $endDateTime->format('H:i:s');

        // Check if the appointment time is available
        // This query finds any appointments that overlap with the requested time slot
        $conflictingAppointments = Appointment::where('appointment_date', $appointmentDate)
            ->where('status', '!=', 'cancelled')
            ->where(function ($query) use ($startTime, $endTime) {
                // Case 1: New appointment starts during an existing appointment
                $query->where(function ($q) use ($startTime, $endTime) {
                    $q->where('start_time', '<=', $startTime)
                        ->where('end_time', '>', $startTime);
                })
                    // Case 2: New appointment ends during an existing appointment
                    ->orWhere(function ($q) use ($startTime, $endTime) {
                        $q->where('start_time', '<', $endTime)
                            ->where('end_time', '>=', $endTime);
                    })
                    // Case 3: New appointment completely contains an existing appointment
                    ->orWhere(function ($q) use ($startTime, $endTime) {
                        $q->where('start_time', '>=', $startTime)
                            ->where('end_time', '<=', $endTime);
                    })
                    // Case 4: New appointment is completely contained within an existing appointment
                    ->orWhere(function ($q) use ($startTime, $endTime) {
                        $q->where('start_time', '<=', $startTime)
                            ->where('end_time', '>=', $endTime);
                    });
            })
            ->count();

        return response()->json([
            'available' => $conflictingAppointments === 0,
            'date' => $appointmentDate,
            'startTime' => $startTime,
            'endTime' => $endTime,
            'duration' => $duration,
            'conflictingCount' => $conflictingAppointments
        ]);
    }

    /**
     * Get business hours.
     *
     * @return \Illuminate\Http\Response
     */
    public function getBusinessHours()
    {
        $businessHours = \App\Models\BusinessHour::all();
        return response()->json($businessHours);
    }
}
