<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Update the authenticated user's profile
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateProfile(Request $request)
    {
        $user = $request->user();
        // Update only the fields that are present in the request
        $updateData = $request->only(['name', 'phone_number', 'notes']);
        $user->update($updateData);

        return response()->json([
            'status' => 'success',
            'message' => 'Profile updated successfully',
            'user' => $user
        ]);
    }
}
