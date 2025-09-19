<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class UserController extends Controller
{
    public function index()
    {
        $users = User::all();
        return response()->json($users);
    }

    public function show($id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json(['error' => 'Category not found'], Response::HTTP_NOT_FOUND);
        }

        return response()->json($user);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            '_id' => 'nullable',
            'name' => 'nullable',
            'phone' => 'nullable',
            'email' => 'nullable',
            'address' => 'nullable',
        ]);
        $user = User::create($validated);
        return response()->json($user, Response::HTTP_CREATED);
    }

    public function update(Request $request, $id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json(['error' => 'Category not found'], Response::HTTP_NOT_FOUND);
        }

        $validated = $request->validate([
            'name' => 'sometimes'
        ]);

        $user->update($validated);
        return response()->json($user);
    }

    public function destroy($id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json(['error' => 'Category not found'], Response::HTTP_NOT_FOUND);
        }

        $user->delete();
        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
