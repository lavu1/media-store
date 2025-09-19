<?php


namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CustomerController extends Controller
{
    public function index()
    {
        $customers = Customer::all();
        return response()->json($customers);
    }

    public function show($id)
    {
        $customer = Customer::find($id);

        if (!$customer) {
            return response()->json(['error' => 'Customer not found'], Response::HTTP_NOT_FOUND);
        }

        return response()->json($customer);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            '_id' => 'required',
            'name' => 'required',
            'phone' => 'nullable',
            'email' => 'nullable',
            'address' => 'nullable'
        ]);

        $customer = Customer::create($validated);
        return response()->json($customer, Response::HTTP_CREATED);
    }

    public function update(Request $request, $id)
    {
        $customer = Customer::find($id);

        if (!$customer) {
            return response()->json(['error' => 'Customer not found'], Response::HTTP_NOT_FOUND);
        }

        $validated = $request->validate([
            'name' => 'sometimes|required|string',
            'phone' => 'nullable|string',
            'email' => 'nullable|email',
            'address' => 'nullable|string'
        ]);

        $customer->update($validated);
        return response()->json($customer);
    }

    public function destroy($id)
    {
        $customer = Customer::find($id);

        if (!$customer) {
            return response()->json(['error' => 'Customer not found'], Response::HTTP_NOT_FOUND);
        }

        $customer->delete();
        return response()->json(null, Response::HTTP_NO_CONTENT);
    }

    public function onHold()
    {
        $transaction = Transaction::whereNot('customer_id',0)->where('status',0)->where('paid',null)->whereNot('ref_number','')->with('customer')->get();

        if (!$transaction) {
            return response()->json(['error' => 'Transaction not found'], Response::HTTP_NOT_FOUND);
        }

        return response()->json($transaction);
    }
}
