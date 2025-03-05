<?php

namespace App\Http\Controllers\Dashboard\Pages;

use App\Http\Controllers\BaseController;
use App\Models\Service;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class ServiceController extends BaseController
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = auth()->user();
        return view('dashboard.pages.service.index', compact('user'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return $this->componentResponse(view('dashboard.pages.service.modal.create'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'service_name' => 'required|string',
            'description' => 'string',
            'duration_minutes' => 'required|integer',
            'price' => 'required|numeric',
            'status' => 'boolean'
        ]);

        Service::create($request->all());
        return $this->modalToastResponse('Service created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $service = Service::find($id);
        return $this->componentResponse(view('dashboard.pages.service.modal.show', compact('service')));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $service = Service::find($id);
        return $this->componentResponse(view('dashboard.pages.service.modal.edit', compact('service')));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        $request->validate([
            'service_name' => 'required|string',
            'description' => 'string',
            'duration_minutes' => 'required|integer',
            'price' => 'required|numeric',
            'status' => 'boolean'
        ]);

        $service = Service::find($request->id);
        $service->update($request->all());
        return $this->modalToastResponse('Service updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $service = Service::find($id);
        $service->delete();
        return response()->json(['message' => 'Service deleted successfully']);
    }

    public function datatable(Request $request)
    {
        $search = request()->get('search');
        $value = isset($search['value']) ? $search['value'] : null;

        $services = Service::select(
            'id',
            'service_name',
            'duration_minutes',
            'price',
            'status',
            'created_at',
        )
            ->when($value, function ($query) use ($value) {
                return $query->where(function ($query) use ($value) {
                    $query->where('service_name', 'like', '%' . $value . '%')
                        ->orWhere('description', 'like', '%' . $value . '%')
                        ->orWhere('duration_minutes', 'like', '%' . $value . '%')
                        ->orWhere('price', 'like', '%' . $value . '%')
                        ->orWhere('status', 'like', '%' . $value . '%');
                });
            });

        return DataTables::of($services->latest())
            ->editColumn('created_at', function ($service) {
                return $service->created_at->diffForHumans();
            })
            ->make(true);
    }

    public function status(String $id)
    {
        // dd($id);

        $service = Service::find($id);
        if ($service->status == 'available') {
            $service->update(['status' => 'unavailable']);
        } else {
            $service->update(['status' => 'available']);
        }
        return response()->json(['message' => 'Service status updated successfully']);
    }
}
