<?php

namespace App\Http\Controllers\Dashboard\Pages;

use App\Http\Controllers\BaseController;
use App\Models\BusinessHour;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class BusinessHourController extends BaseController
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = auth()->user();
        return view('dashboard.pages.businessHour.index', compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $businessHour = BusinessHour::find($id);
        return $this->componentResponse(view('dashboard.pages.businessHour.modal.edit', compact('businessHour')));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        $request->validate([
            'day_of_week' => 'required|string',
            'open_time' => 'required|string',
            'close_time' => 'required|string',
            'is_closed' => 'boolean',
        ]);

        $request->merge(['is_closed' => $request->is_closed ? 1 : 0]);

        $businessHour = BusinessHour::find($request->id);
        $businessHour->update($request->all());
        return $this->modalToastResponse('Business Hour updated successfully');
    }

    public function datatable(Request $request)
    {
        $search = request()->get('search');
        $value = isset($search['value']) ? $search['value'] : null;

        $businessHours = BusinessHour::select(
            'id',
            'day_of_week',
            'open_time',
            'close_time',
            'is_closed',
            'created_at',
        )
            ->when($value, function ($query) use ($value) {
                return $query->where(function ($query) use ($value) {
                    $query->where('day_of_week', 'like', '%' . $value . '%')
                        ->orWhere('open_time', 'like', '%' . $value . '%')
                        ->orWhere('close_time', 'like', '%' . $value . '%')
                        ->orWhere('is_closed', 'like', '%' . $value . '%');
                });
            });

        return DataTables::of($businessHours->get())
            ->editColumn('created_at', function ($businessHour) {
                return $businessHour->created_at->diffForHumans();
            })
            ->make(true);
    }
}
