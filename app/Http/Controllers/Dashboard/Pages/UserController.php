<?php

namespace App\Http\Controllers\Dashboard\Pages;

use App\Http\Controllers\BaseController;
use App\Models\User;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class UserController extends BaseController
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = auth()->user();
        return view('dashboard.pages.user.index', compact('user'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return $this->componentResponse(view('dashboard.pages.user.modal.create'));
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

        User::create($request->all());
        return $this->modalToastResponse('User created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $user = User::find($id);
        return $this->componentResponse(view('dashboard.pages.user.modal.show', compact('user')));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $user = User::find($id);
        return $this->componentResponse(view('dashboard.pages.user.modal.edit', compact('user')));
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

        $user = User::find($request->id);
        $user->update($request->all());
        return $this->modalToastResponse('User updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user = User::find($id);
        $user->delete();
        return response()->json(['message' => 'User deleted successfully']);
    }

    public function datatable(Request $request)
    {
        $search = request()->get('search');
        $value = isset($search['value']) ? $search['value'] : null;

        $users = User::select(
            'id',
            'name',
            'email',
            'email_verified_at',
            'status',
            'created_at'
        )
            ->when($value, function ($query) use ($value) {
                return $query->where(function ($query) use ($value) {
                    $query->where('name', 'like', '%' . $value . '%')
                        ->orWhere('email', 'like', '%' . $value . '%');
                });
            });

        if ($request->status) {
            $users->where('status', $request->status);
        }

        if ($request->verified) {
            $users->where('email_verified_at', '!=', null);
        }

        if ($request->not_verified) {
            $users->where('email_verified_at', null);
        }

        return datatables::of($users->latest())
            ->editColumn('created_at', function ($user) {
                return $user->created_at->diffForHumans();
            })
            ->make(true);
    }

    public function status(string $id)
    {
        $user = User::find($id);
        if ($user->status == 'active') {
            $user->update(['status' => 'inactive']);
        } else {
            $user->update(['status' => 'active']);
        }
        return response()->json(['message' => 'User status updated successfully']);
    }
}