<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\URL;

class InventoryController extends Controller
{
    public function index()
    {
        $inventory = Inventory::with('category')->get();
        return response()->json($inventory);
    }

    public function show($id)
    {
        $inventory = Inventory::with('category')->find($id);

        if (!$inventory) {
            return response()->json(['error' => 'Inventory item not found'], Response::HTTP_NOT_FOUND);
        }

        return response()->json($inventory);
    }
/*
    public function store(Request $request)
    {
        $validated = $request->validate([
            '_id' => 'required|string|unique:inventory',
            'barcode' => 'nullable|string',
            'expiration_date' => 'nullable|date',
            'price' => 'nullable|numeric|min:0',
            'category_id' => 'nullable|string|exists:categories,_id',
            'quantity' => 'nullable|integer|min:0',
            'name' => 'required|string|max:255',
            'stock' => 'nullable|integer|min:0',
            'min_stock' => 'nullable|integer|min:0',
            'img' => 'nullable|string'
        ]);

        $inventory = Inventory::create($validated);
        return response()->json($inventory, Response::HTTP_CREATED);
    }
    */
    public function store(Request $request)
    {
       // dd($request);
        $validated = $request->validate([
            'id' => 'nullable|string',
            'remove' => 'nullable|string',
            'barcode' => 'nullable|string',
            'expirationDate' => 'nullable',
            'price' => 'nullable',
            'category' => 'nullable|string',
            'quantity' => 'nullable',
            'name' => 'required|string',
            'stock' => 'nullable',
            'minStock' => 'nullable',
            'img' => 'nullable', // image file
            'imagename' => 'nullable'
        ]);

        // Handle image upload if exists
        if ($request->hasFile('imagename')) {
            $file = $request->file('imagename');
            //$fileName = time().'_'.$file->getClientOriginalName();
            $fileName = time().'_inventory_image.'.$file->extension();

            // store in public/images
            $file->move(public_path('images'), $fileName);

            // generate full URL (works on localhost too)
            $validated['img'] = URL::to('/images/'.$fileName);
        }
//dd($validated);
        $validated['expiration_date'] = $validated['expirationDate'] ?? null;
        $validated['category_id'] = $validated['category'] ?? null;
        $validated['min_stock'] = $validated['minStock'] ?? null;
        $validated['stock']?? $validated['stock'] = 'off';
        $validated['stock'] = ($validated['stock']=='on')? 0: 1;
       // dd($validated);
        if ($validated['id'] == '') {
            $inventory = Inventory::create($validated);
        }else{
            $id = $request->input('id');
            $inventory = Inventory::find($id);
            $inventory->update($validated);
        }

        return response()->json($inventory, Response::HTTP_CREATED);
    }
    public function update(Request $request)
    {
        $id = $request->input('id');
        $inventory = Inventory::find($id);

        if (!$inventory) {
            return response()->json(['error' => 'Inventory item not found'], Response::HTTP_NOT_FOUND);
        }
        $validated = $request->validate([
            'id' => 'nullable|string',
            'remove' => 'nullable|string',
            'barcode' => 'nullable|string',
            'expirationDate' => 'nullable',
            'price' => 'nullable',
            'category' => 'nullable|string',
            'quantity' => 'nullable',
            'name' => 'required|string',
            'stock' => 'nullable',
            'minStock' => 'nullable',
            'img' => 'nullable', // image file
            'imagename' => 'nullable'
        ]);

        // Handle image upload if exists
        if ($request->hasFile('imagename')) {
            $file = $request->file('imagename');
            $fileName = time().'_'.$file->getClientOriginalName();

            // store in public/images
            $file->move(public_path('images'), $fileName);

            // generate full URL (works on localhost too)
            $validated['img'] = URL::to('/images/'.$fileName);
        }
//dd($validated);
        $validated['expiration_date'] = $validated['expirationDate'] ?? null;
        $validated['category_id'] = $validated['category'] ?? null;
        $validated['min_stock'] = $validated['minStock'] ?? null;
        $validated['stock'] = ($validated['stock']=='on')? 0: 1;
        $inventory->update($validated);
        return response()->json($inventory);
    }

    public function destroy($id)
    {
        $inventory = Inventory::find($id);

        if (!$inventory) {
            return response()->json(['error' => 'Inventory item not found'], Response::HTTP_NOT_FOUND);
        }

        $inventory->delete();
        return response()->json(null, Response::HTTP_NO_CONTENT);
    }

    public function getSku(Request $request)
    {
        $sku = $request->input('skuCode');
        $inventory = Inventory::with('category')->where('barcode',$sku)->first();


        if (!$inventory) {
            return response()->json(['error' => 'Inventory item not found'], Response::HTTP_NOT_FOUND);
        }

        return response()->json($inventory);
    }





    public function lowStock()
    {
        $lowStockItems = Inventory::whereRaw('stock <= min_stock')->get();
        return response()->json($lowStockItems);
    }
}
