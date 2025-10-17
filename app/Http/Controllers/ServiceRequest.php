<?php

namespace App\Http\Controllers;

use App\Models\ServiceRequests;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\URL;

class ServiceRequest extends Controller
{

    public function index()
    {
        $serviceRequests = ServiceRequests::where('type','Share me Jobs')->where('status','pending')->orderBy('id','asc')->get();
        return response()->json($serviceRequests);
    }

    public function show($id)
    {
        $serviceRequests = ServiceRequests::find($id);

        if (!$serviceRequests) {
            return response()->json(['error' => 'service Requests item not found'], Response::HTTP_NOT_FOUND);
        }

        return response()->json($serviceRequests);
    }
    public function update(Request $request,$id)
    {
        //$id = $request->input('id');
        $serviceRequests = ServiceRequests::find($id);

        if (!$serviceRequests) {
            return response()->json(['error' => 'service Requests item not found'], Response::HTTP_NOT_FOUND);
        }
        $validated = $request->validate([
            'days' => 'nullable|string',
            'status' => 'nullable|string',
        ]);

//dd($validated);
        $validated['days'] = $validated['days'] ?? '1';
        $validated['status'] = $validated['status'] ?? 'pending';
        $serviceRequests->update($validated);
        return response()->json($serviceRequests);
    }

    public function destroy($id)
    {
        $inventory = ServiceRequests::find($id);

        if (!$inventory) {
            return response()->json(['error' => 'Inventory item not found'], Response::HTTP_NOT_FOUND);
        }

        $inventory->delete();
        return response()->json(null, Response::HTTP_NO_CONTENT);
    }


    public function store(Request $request)
    {
        $validated = $request->validate([
            'type' => 'nullable',
            'days' => 'nullable',
            'name' => 'nullable',
            'email' => 'nullable',
            'phone' => 'nullable',
            'education_background' => 'nullable',
            'work_experience' => 'nullable',
            'skills' => 'nullable',
            'status' => 'nullable',
            'cv_file_path' => 'nullable',
           // 'img' => 'nullable', // image file
            'additional_notes' => 'nullable'
        ]);

        $validated['phone'] = $this->sanitizePhoneNumber($validated['phone']);

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
            $validated['cv_file_path'] = URL::to('/images/'.$path);
            //dd($validated['img']);
        }
        // dd($validated);
            $inventory = ServiceRequests::create($validated);

        return response()->json($inventory, Response::HTTP_CREATED);
    }
    public function sanitizePhoneNumber(?string $phone): ?string
    {
        if (!$phone || $phone === 'null') {
            return null; // Keep null values as null
        }

        // Remove all non-digit characters first (spaces, slashes, +, etc.)
        $cleaned = preg_replace('/\D/', '', $phone);

        // Handle multiple numbers separated by slashes - take first one
        if (str_contains($cleaned, '/')) {
            $cleaned = explode('/', $cleaned)[0];
        }

        // Handle multiple numbers with spaces - take first one
        if (strlen($cleaned) > 12) {
            // If it's too long, it might contain multiple numbers
            // Try to find a valid 9-digit number after the country code
            if (preg_match('/(260)?(\d{9})/', $cleaned, $matches)) {
                $cleaned = '260' . $matches[2];
            } else {
                // If no clear pattern, take first 12 digits
                $cleaned = substr($cleaned, 0, 12);
            }
        }

        // Convert to standard +260 format
        if (str_starts_with($cleaned, '260') && strlen($cleaned) === 12) {
            // Already in correct format (without +)
            return '+' . $cleaned;
        } elseif (str_starts_with($cleaned, '0') && strlen($cleaned) === 10) {
            // Format: 0XXXXXXXXX -> +260XXXXXXXX
            return '+260' . substr($cleaned, 1);
        } elseif (strlen($cleaned) === 9) {
            // Format: XXXXXXXXX -> +260XXXXXXXX
            return '+260' . $cleaned;
        } elseif (str_starts_with($cleaned, '260') && strlen($cleaned) > 12) {
            // Has 260 prefix but too long - take first 12 digits
            return '+' . substr($cleaned, 0, 12);
        } elseif (strlen($cleaned) === 12 && !str_starts_with($cleaned, '260')) {
            // 12 digits but wrong prefix
            return '+260' . substr($cleaned, 3);
        }

        // Final validation
        if (strlen($cleaned) !== 12 || !str_starts_with($cleaned, '260')) {
            \Log::warning("Invalid phone number format: {$phone} -> {$cleaned}");
            return null;
        }

        return '+' . $cleaned;
    }
}
