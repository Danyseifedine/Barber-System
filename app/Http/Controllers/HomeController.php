<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Feedback;
use App\Models\Service;

class HomeController extends BaseController
{
    public function index()
    {
        $services = Service::all();
        return view('home', compact('services'));
    }

    public function contact(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email',
            'subject' => 'required',
            'message' => 'required',
        ]);

        Feedback::create($request->all());


        return $this->successToastResponse('Message sent successfully');
    }

    public function services()
    {
        $services = Service::all();
        return view('services', compact('services'));
    }
}
