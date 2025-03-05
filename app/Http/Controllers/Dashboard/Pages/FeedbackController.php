<?php

namespace App\Http\Controllers\Dashboard\Pages;

use App\Http\Controllers\BaseController;
use App\Models\Feedback;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class FeedbackController extends BaseController
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = auth()->user();
        return view('dashboard.pages.feedback.index', compact('user'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return $this->componentResponse(view('dashboard.pages.feedback.modal.create'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
                    'email' => 'required|string',
        ]);

        Feedback::create($request->all());
        return $this->modalToastResponse('Feedback created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $feedback = Feedback::find($id);
        return $this->componentResponse(view('dashboard.pages.feedback.modal.show', compact('feedback')));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $feedback = Feedback::find($id);
        return $this->componentResponse(view('dashboard.pages.feedback.modal.edit', compact('feedback')));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
                    'email' => 'required|string',
        ]);

        $feedback = Feedback::find($request->id);
        $feedback->update($request->all());
        return $this->modalToastResponse('Feedback updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $feedback = Feedback::find($id);
        $feedback->delete();
        return response()->json(['message' => 'Feedback deleted successfully']);
    }

    public function datatable(Request $request)
    {
        $search = request()->get('search');
        $value = isset($search['value']) ? $search['value'] : null;

        $feedbacks = Feedback::select(
        'id',
        'name',
                'email',
                'subject',
                'message',
        'created_at',
        )
            ->when($value, function ($query) use ($value) {
                return $query->where(function ($query) use ($value) {
                    $query->where('name', 'like', '%' . $value . '%')
                                ->orWhere('email', 'like', '%' . $value . '%')
                                ->orWhere('subject', 'like', '%' . $value . '%')
                                ->orWhere('message', 'like', '%' . $value . '%');
                });
            });

        return DataTables::of($feedbacks->latest())
            ->editColumn('created_at', function ($feedback) {
                return $feedback->created_at->diffForHumans();
            })
            ->make(true);
    }
}