<?php

namespace App\Http\Controllers;

use App\Models\Service;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    /**
     * Get all available services
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAllServices()
    {
        $services = Service::where('status', 1)
            ->orderBy('service_name')
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $services
        ]);
    }
}
