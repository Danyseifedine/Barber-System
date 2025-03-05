<?php

namespace App\Http\Controllers\Dashboard\Pages;

use App\Http\Controllers\BaseController;
use App\Models\Appointment;
use App\Models\User;
use App\Models\Service;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class AppointmentController extends BaseController
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = auth()->user();
        return view('dashboard.pages.appointment.overview.index', compact('user'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $users = User::all();
        $services = Service::all();
        return $this->componentResponse(view('dashboard.pages.appointment.overview.modal.create', compact('users', 'services')));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required',
            'appointment_date' => 'required',
            'start_time' => 'required',
            'end_time' => 'required',
            'service_ids' => 'required|array',
            'service_ids.*' => 'exists:services,id',
            'status' => 'boolean'
        ]);

        $appointment = Appointment::create($request->except('service_ids'));

        if ($request->has('service_ids')) {
            foreach ($request->service_ids as $serviceId) {
                $appointment->appointmentServices()->create([
                    'service_id' => $serviceId
                ]);
            }
        }

        return $this->modalToastResponse('Appointment created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $appointment = Appointment::find($id);
        return $this->componentResponse(view('dashboard.pages.appointment.overview.modal.show', compact('appointment')));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $services = Service::all();
        $appointment = Appointment::find($id);
        $users = User::all();
        return $this->componentResponse(view('dashboard.pages.appointment.overview.modal.edit', compact('appointment', 'users', 'services')));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        $request->validate([
            'user_id' => 'required',
            'appointment_date' => 'required',
            'start_time' => 'required',
            'end_time' => 'required',
            'status' => 'boolean',
            'service_ids' => 'required|array',
            'service_ids.*' => 'exists:services,id',
        ]);

        $appointment = Appointment::find($request->id);
        $appointment->update($request->except('service_ids'));

        if ($request->has('service_ids')) {
            $appointment->appointmentServices()->delete();
            foreach ($request->service_ids as $serviceId) {
                $appointment->appointmentServices()->create([
                    'service_id' => $serviceId
                ]);
            }
        }

        // If the appointment is completed, update the payment amount
        if ($appointment->status == 'completed') {
            // Refresh the appointment to get the updated services
            $appointment->load('appointmentServices.service');

            $totalPrice = $appointment->appointmentServices->sum(function ($appointmentService) {
                return $appointmentService->service->price;
            });

            // Update or create payment
            if ($appointment->payment) {
                $appointment->payment->update(['amount' => $totalPrice]);
            } else {
                $appointment->payment()->create([
                    'amount' => $totalPrice
                ]);
            }
        } else if ($appointment->payment) {
            // If appointment is not completed but has a payment, delete it
            $appointment->payment->delete();
        }

        return $this->modalToastResponse('Appointment updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $appointment = Appointment::find($id);
        $appointment->appointmentServices()->delete();
        $appointment->payment()->delete();
        $appointment->delete();
        return response()->json(['message' => 'Appointment deleted successfully']);
    }

    public function datatable(Request $request)
    {
        $search = request()->get('search');
        $value = isset($search['value']) ? $search['value'] : null;

        $appointments = Appointment::with(['user', 'appointmentServices.service'])->select(
            'id',
            'user_id',
            'appointment_date',
            'start_time',
            'end_time',
            'status',
            'created_at',
        )
            ->when($value, function ($query) use ($value) {
                return $query->where(function ($query) use ($value) {
                    $query->where('user_id', 'like', '%' . $value . '%')
                        ->orWhere('appointment_date', 'like', '%' . $value . '%')
                        ->orWhere('start_time', 'like', '%' . $value . '%')
                        ->orWhere('end_time', 'like', '%' . $value . '%')
                        ->orWhere('status', 'like', '%' . $value . '%')
                        ->orWhere('notes', 'like', '%' . $value . '%');
                });
            });

        return DataTables::of($appointments->latest())
            ->editColumn('created_at', function ($appointment) {
                return $appointment->created_at->diffForHumans();
            })
            ->addColumn('final_price', function ($appointment) {
                $totalPrice = $appointment->appointmentServices->sum(function ($appointmentService) {
                    return $appointmentService->service->price;
                });
                return '$' . number_format($totalPrice, 2);
            })
            ->editColumn('user_id', function ($appointment) {
                return $appointment->user->name;
            })
            ->make(true);
    }

    public function status(string $status, string $id)
    {
        $appointment = Appointment::with('appointmentServices.service')->findOrFail($id);
        $oldStatus = $appointment->status;
        $appointment->status = $status;
        $appointment->save();

        // Handle payment creation when appointment is completed
        if ($status == 'completed') {
            $totalPrice = $appointment->appointmentServices->sum(function ($appointmentService) {
                return $appointmentService->service->price;
            });

            // Check if payment already exists, update it or create new one
            if ($appointment->payment) {
                $appointment->payment->update(['amount' => $totalPrice]);
            } else {
                $appointment->payment()->create([
                    'amount' => $totalPrice
                ]);
            }
        }

        // Handle payment deletion when appointment is cancelled or rescheduled from any status
        if ($status == 'cancelled' || ($status == 'scheduled' && $oldStatus == 'completed')) {
            // Delete any existing payment regardless of previous status
            if ($appointment->payment) {
                $appointment->payment->delete();
            }
        }

        // Build response message based on status change
        $message = 'Appointment ';
        switch ($status) {
            case 'scheduled':
                $message .= $oldStatus == 'cancelled' ? 'has been rescheduled' : 'status updated to scheduled';
                break;
            case 'completed':
                $message .= 'has been marked as completed';
                break;
            case 'cancelled':
                $message .= 'has been cancelled';
                break;
            default:
                $message .= 'status updated successfully';
        }

        return response()->json([
            'message' => $message,
            'status' => $status,
            'oldStatus' => $oldStatus
        ]);
    }
}
