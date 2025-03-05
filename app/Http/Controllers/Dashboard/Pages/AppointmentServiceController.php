<?php

namespace App\Http\Controllers\Dashboard\Pages;

use App\Http\Controllers\BaseController;
use App\Models\Appointment;
use App\Models\AppointmentService;
use App\Models\Service;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class AppointmentServiceController extends BaseController
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = auth()->user();
        return view('dashboard.pages.appointment.service.index', compact('user'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $services = Service::all();
        $appointments = Appointment::all();
        return $this->componentResponse(view('dashboard.pages.appointment.service.modal.create', compact('services', 'appointments')));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'appointment_id' => 'required',
            'service_id' => 'required'
        ]);

        AppointmentService::create($request->all());
        return $this->modalToastResponse('AppointmentService created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $appointmentService = AppointmentService::find($id);
        return $this->componentResponse(view('dashboard.pages.appointment.service.modal.show', compact('appointmentService')));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {

        $appointmentService = AppointmentService::find($id);
        $services = Service::all();
        $appointments = Appointment::all();
        return $this->componentResponse(view('dashboard.pages.appointment.service.modal.edit', compact('appointmentService', 'services', 'appointments')));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        $request->validate([
            'appointment_id' => 'required',
            'service_id' => 'required'
        ]);

        $appointmentService = AppointmentService::find($request->id);
        $appointmentService->update($request->all());
        return $this->modalToastResponse('Appointment Service updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $appointmentService = AppointmentService::find($id);
        $appointmentService->delete();
        return response()->json(['message' => 'Appointment Service deleted successfully']);
    }

    public function datatable(Request $request)
    {
        $search = request()->get('search');
        $value = isset($search['value']) ? $search['value'] : null;

        $appointmentServices = AppointmentService::select(
            'id',
            'appointment_id',
            'service_id',
            'created_at',
        )
            ->when($value, function ($query) use ($value) {
                return $query->where(function ($query) use ($value) {
                    $query->where('appointment_id', 'like', '%' . $value . '%')
                        ->orWhere('service_id', 'like', '%' . $value . '%');
                });
            });

        return DataTables::of($appointmentServices->latest())
            ->editColumn('created_at', function ($appointmentService) {
                return $appointmentService->created_at->diffForHumans();
            })
            ->editColumn('appointment_id', function ($appointmentService) {
                return $appointmentService->appointment->appointment_date;
            })
            ->editColumn('service_id', function ($appointmentService) {
                return $appointmentService->service->service_name;
            })
            ->addColumn('actions', function ($appointmentService) {
                return actionButtons($appointmentService->id);
            })
            ->make(true);
    }
}
