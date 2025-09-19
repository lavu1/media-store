<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\URL;

class SettingController extends Controller
{
    public function index()
    {
        $settings = Setting::all();
        return ['_id' => $settings[0]->_id, 'settings' => $settings[0]->settings];
    }

    public function show($id)
    {
        $setting = Setting::find($id);

        if (!$setting) {
            return response()->json(['error' => 'Setting not found'], Response::HTTP_NOT_FOUND);
        }

        return response()->json($setting);
    }

    public function store(Request $request)
    {

        $validated = $request->validate([
            '_id' => 'nullable',
            'img' => 'nullable',
            'remove' => 'nullable',
            'store' => 'nullable',
            'address_one' => 'nullable',
            'address_two' => 'nullable',
            'contact' => 'nullable',
            'tax' => 'nullable',
            'symbol' => 'nullable',
            'percentage' => 'nullable',
            'charge_tax' => 'nullable',
            'imagename' => 'nullable',
            'footer' => 'nullable',
            'app' => 'nullable',
        ]);
        if ($request->hasFile('img')) {
            $file = $request->file('img');
            $fileName = time().'_'.$file->getClientOriginalName();

            // store in public/images
            $file->move(public_path('images'), $fileName);

            // generate full URL (works on localhost too)
            $validated['img'] = URL::to('/images/'.$fileName);
        }
        $validated['settings'] =  "{'app': '[object Object]','store': ".$validated['store'].",
        'address_one': ".$validated['address_one'].",
        'address_two': ".$validated['address_two'].",',
        'contact': ".$validated['contact'].",'',
        'tax': ".$validated['tax'].",'',
        'symbol': ".$validated['symbol'].",',
        'percentage': ".$validated['percentage'].",',
        'charge_tax': ".$validated['charge_tax'].",'',
        'footer': ".$validated['footer'].",'',
        'img': ".$validated['img'].",','
        }";

        $setting = Setting::create($validated);
        return response()->json($setting, Response::HTTP_CREATED);
    }

    public function update(Request $request, $id)
    {
        $setting = Setting::find($id);

        if (!$setting) {
            return response()->json(['error' => 'Setting not found'], Response::HTTP_NOT_FOUND);
        }

        $validated = $request->validate([
            'settings' => 'sometimes|required|array'
        ]);

        $setting->update($validated);
        return response()->json($setting);
    }

    public function destroy($id)
    {
        $setting = Setting::find($id);

        if (!$setting) {
            return response()->json(['error' => 'Setting not found'], Response::HTTP_NOT_FOUND);
        }

        $setting->delete();
        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
