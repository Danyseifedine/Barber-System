<?php

namespace App\Http\Controllers\Dashboard\Pages;

use App\Http\Controllers\BaseController;
use App\Models\Payment;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class PaymentController extends BaseController
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = auth()->user();
        return view('dashboard.pages.payment.index', compact('user'));
    }


    public function datatable(Request $request)
    {
        $search = request()->get('search');
        $value = isset($search['value']) ? $search['value'] : null;

        $payments = Payment::select(
            'id',
            'appointment_id',
            'amount',
            'created_at',
        )
            ->when($value, function ($query) use ($value) {
                return $query->where(function ($query) use ($value) {
                    $query->where('appointment_id', 'like', '%' . $value . '%')
                        ->orWhere('amount', 'like', '%' . $value . '%');
                });
            });

        return DataTables::of($payments->latest())
            ->editColumn('created_at', function ($payment) {
                return $payment->created_at->diffForHumans();
            })
            ->make(true);
    }
}
