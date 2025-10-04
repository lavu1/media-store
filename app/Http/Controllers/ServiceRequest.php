<?php

namespace App\Http\Controllers;

use App\Models\ServiceRequests;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\URL;

class ServiceRequest extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'type' => 'nullable|string',
            'days' => 'nullable|string',
            'name' => 'nullable|string',
            'email' => 'nullable',
            'phone' => 'nullable',
            'education_background' => 'nullable|string',
            'work_experience' => 'nullable',
            'skills' => 'required|string',
            'status' => 'nullable',
            'cv_file_path' => 'nullable',
           // 'img' => 'nullable', // image file
            'additional_notes' => 'nullable'
        ]);

        // Handle image upload if exists
        if ($request->hasFile('cv_file_path')) {
            $file = $request->file('cv_file_path');
            //$fileName = time().'_'.$file->getClientOriginalName();
            $fileName = time().'_inventory_image.'.$file->extension();

            // store in public/images
            //$file->move(public_path('images'), $fileName);
            //$file->move(base_path('images'), $fileName);
            $path = $request->file('cv_file_path')->store('', 'public');


            // generate full URL (works on localhost too)
            $validated['cv_file_path'] = URL::to('/files/'.$path);
            //dd($validated['img']);
        }
        // dd($validated);
            $inventory = ServiceRequests::create($validated);

        return response()->json($inventory, Response::HTTP_CREATED);
    }
}
