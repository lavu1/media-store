<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Log;



class TransactionController extends Controller
{
    public function index()
    {
        $transactions = Transaction::with('customer')->get();
        return response()->json($transactions);
    }

    public function show($id)
    {
        $transaction = Transaction::with('customer')->find($id);

        if (!$transaction) {
            return response()->json(['error' => 'Transaction not found'], Response::HTTP_NOT_FOUND);
        }

        return response()->json($transaction);
    }
    public function onHold()
    {
        $transaction = Transaction::where('status',0)->where('paid',null)->whereNot('ref_number','')->with('customer')->get();

        if (!$transaction) {
            return response()->json(['error' => 'Transaction not found'], Response::HTTP_NOT_FOUND);
        }

        return response()->json($transaction);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            //'_id' => 'nullable',
            'order' => 'nullable',
            'ref_number' => 'nullable',
            'discount' => 'nullable',
            'customer_id' => 'nullable',
            'status' => 'nullable',
            'subtotal' => 'nullable',
            'tax' => 'nullable',
            'order_type' => 'nullable',
            'items' => 'nullable',
            'date' => 'nullable',
            'payment_type' => 'nullable',
            'payment_info' => 'nullable',
            'total' => 'nullable',
            'paid' => 'nullable',
            'change' => 'nullable',
            'till' => 'nullable',
            'customer' => 'nullable',
            'user' => 'nullable',
            'user_id' => 'nullable'
        ]);
       // dd(json_encode(['customer'=> $validated['customer']]));
        $validated['customer_id']=$validated['customer']['id']??0;
        $validated['customer'] = $validated['customer']['name']??0;
        $transaction = Transaction::create($validated);
        return response()->json($transaction, Response::HTTP_CREATED);
    }

    public function update(Request $request, $id)
    {
        $transaction = Transaction::find($id);

        if (!$transaction) {
            return response()->json(['error' => 'Transaction not found'], Response::HTTP_NOT_FOUND);
        }

        $validated = $request->validate([
            'order' => 'nullable',
            'ref_number' => 'nullable',
            'discount' => 'nullable',
            'customer_id' => 'nullable',
            'status' => 'nullable',
            'subtotal' => 'nullable',
            'tax' => 'nullable',
            'order_type' => 'nullable',
            'items' => 'nullable|array',
            'customer' => 'nullable',
            'date' => 'nullable|date',
            'payment_type' => 'nullable',
            'payment_info' => 'nullable',
            'total' => 'nullable',
            'paid' => 'nullable',
            'change' => 'nullable',
            'till' => 'nullable',
            'user' => 'nullable',
            'user_id' => 'nullable'
        ]);
        $validated['customer_id'] == NULL ? $validated['customer'] = 0 : $validated['customer']['id'];

        $transaction->update($validated);
        return response()->json($transaction);
    }

    public function destroy($id)
    {
        $transaction = Transaction::find($id);

        if (!$transaction) {
            return response()->json(['error' => 'Transaction not found'], Response::HTTP_NOT_FOUND);
        }

        $transaction->delete();
        return response()->json(null, Response::HTTP_NO_CONTENT);
    }

    public function byDate(Request $request)
    {
        $start = $request->input('start', 'any');
        $end = $request->input('end', 'any');
        $user = $request->input('user', 'any');
        $status = $request->input('status', 'any');
        $till = $request->input('till', 'any');

        // Start building the query on the Transaction model.
        // The `when()` method allows us to conditionally add clauses to the query.
        $query = Transaction::query()
            ->when($start !== 'any' && $end !== 'any', function (Builder $q) use ($start, $end) {
                // Add a whereBetween clause only if both start and end dates are provided and not 'any'.
                // It's good practice to wrap date parsing in a try-catch block.
                try {
                    // Assuming your date column in the database is 'created_at'.
                    $q->whereBetween('created_at', [
                        \Carbon\Carbon::parse($start)->startOfDay(),
                        \Carbon\Carbon::parse($end)->endOfDay(),
                    ]);
                } catch (\Exception $e) {
                    // Log the error if the date format is invalid, but don't break the request.
                    Log::error('Invalid date format provided for transaction search: ' . $e->getMessage());
                }
            })
            ->when($user !== 'any' && $user != 0, function (Builder $q) use ($user) {
                // Add a where clause for the user ID if it's not 'any' or 0.
                // Assuming your user foreign key is 'user_id'.
                $q->where('user_id', $user);
            })
            ->when($status !== 'any', function (Builder $q) use ($status) {
                // Add a where clause for the status if it's not 'any' or 0.
                $q->where('status', $status);
            })
            ->when($till !== 'any' && $till != 0, function (Builder $q) use ($till) {
                // Add a where clause for the till ID if it's not 'any' or 0.
                // Assuming your till foreign key is 'till_id'.
                $q->where('till', $till);
            });

        // Execute the final query to get the collection of transactions.
        $transactions = $query->get();

        // Return the results as a JSON response.
        return response()->json($transactions);
        return response()->json($transactions);
    }

    public function delete(Request $request)
    {
        $transaction = Transaction::find($request->input('orderId'));

        if (!$transaction) {
            return response()->json(['error' => 'Transaction not found'], Response::HTTP_NOT_FOUND);
        }

        $transaction->delete();
        return response()->json(null, Response::HTTP_NO_CONTENT);
    }

    public function ByDates(){
        $transactions = Transaction::with('customer')->get();
    }
}
