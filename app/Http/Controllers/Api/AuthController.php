<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class AuthController extends Controller
{
    // Login
    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required',
            'password' => 'required',
        ]);

        $user = User::where('username', $request->username)->first();

        if (!$user){// || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
            '_id'=>$user->id,
            'username'=>$user->username,
            'fullname'=>$user->fullname,
            'perm_products'=>$user->perm_products,
            'perm_categories'=>$user->perm_categories,
            'perm_transactions'=>$user->perm_transactions,
            'perm_users'=>$user->perm_users,
            'perm_settings'=>$user->perm_settings,
            'pos'=>$user->pos,
            'status'=>$user->status,
            'password'=>$user->password,
            "auth"=> true
            //$user,
        ]);
    }

    // Logout
    public function logout()
    {
        return response()->json(['message' => 'Logged out successfully']);
    }
    public function Check()
    {
        $id = Auth::id();
        $user = User::find($id);;

        if (!$user){// || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'No information'], 404);
        }

        return response()->json([
            '_id'=>$user->id,
            'username'=>$user->username,
            'fullname'=>$user->fullname,
            'perm_products'=>$user->perm_products,
            'perm_categories'=>$user->perm_categories,
            'perm_transactions'=>$user->perm_transactions,
            'perm_users'=>$user->perm_users,
            'perm_settings'=>$user->perm_settings,
            'status'=>$user->status,
            'password'=>$user->password,
            'pos'=>$user->pos,
            "auth"=> true
            //$user,
        ]);
    }
    public function AddUser(Request $request)
    {

        $validated = $request->validate([
//            'username' => 'required',
            'id' => 'nullable',
            '_id' => 'nullable',
            'fullname'=>'nullable',
            'username'=>'nullable',
            'password'=>'nullable',
            'perm_products'=>'nullable',
            'perm_categories'=>'nullable',
            'perm_transactions'=>'nullable',
            'perm_users'=>'nullable',
            'perm_settings'=>'nullable'

        ]);

        $validated['name']  = $validated['fullname'];
        $validated['pos'] = $validated['id']??$validated['_id'];
        $validated['perm_products'] = $validated['perm_products']??0=='on'?1:0;
        $validated['perm_categories']  = $validated['perm_categories']??0=='on'?1:0;
        $validated['perm_transactions']  = $validated['perm_transactions']??0=='on'?1:0;
        $validated['perm_users']  = $validated['perm_users']??0=='on'?1:0;
        $validated['perm_settings']  = $validated['perm_settings']??0=='on'?1:0;
        //dd($validated);
            $validated['id']??$validated['id'] = '';
        if ($validated['id']==''){
            $user = User::create($validated);
        }else{
            $user = User::where('pos',$validated['id'])->first();
            unset($validated['username']);
            $user->update($validated);
        }
        $user = User::where('username', $request->username)->first();
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
            '_id'=>$user->id,
            'username'=>$user->username,
            'fullname'=>$user->fullname,
            'perm_products'=>$user->perm_products,
            'perm_categories'=>$user->perm_categories,
            'perm_transactions'=>$user->perm_transactions,
            'perm_users'=>$user->perm_users,
            'perm_settings'=>$user->perm_settings,
            'status'=>$user->status,
            'pos'=>$user->pos,
            'password'=>$user->password,
            "auth"=> true
            //$user,
        ]);
    }
    public function LatestAddUser(Request $request)
    {

        $validated = $request->validate([
//            'username' => 'required',
            'id' => 'nullable',
            '_id' => 'nullable',
            'fullname'=>'nullable',
            'username'=>'nullable',
            'password'=>'nullable',
            'perm_products'=>'nullable',
            'perm_categories'=>'nullable',
            'perm_transactions'=>'nullable',
            'perm_users'=>'nullable',
            'perm_settings'=>'nullable'

        ]);

        $validated['name']  = $validated['fullname'];
        $validated['pos'] = $validated['id']??$validated['_id'];
        $validated['perm_products'] = $validated['perm_products']??0=='on'?1:0;
        $validated['perm_categories']  = $validated['perm_categories']??0=='on'?1:0;
        $validated['perm_transactions']  = $validated['perm_transactions']??0=='on'?1:0;
        $validated['perm_users']  = $validated['perm_users']??0=='on'?1:0;
        $validated['perm_settings']  = $validated['perm_settings']??0=='on'?1:0;
        //dd($validated);
            $validated['id']??$validated['id'] = '';
        if ($validated['id']==''){
            $user = User::create($validated);
        }else{
            $user = User::where('pos',$validated['id'])->first();
            unset($validated['username']);
            $user->update($validated);
        }
        $user = User::where('username', $request->username)->first();
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
            '_id'=>$user->id,
            'username'=>$user->username,
            'fullname'=>$user->fullname,
            'perm_products'=>$user->perm_products,
            'perm_categories'=>$user->perm_categories,
            'perm_transactions'=>$user->perm_transactions,
            'perm_users'=>$user->perm_users,
            'perm_settings'=>$user->perm_settings,
            'status'=>$user->status,
            'pos'=>$user->pos,
            'password'=>$user->password,
            "auth"=> true
            //$user,
        ]);
    }
    public function GetUser(string $id)
    {
        $user = User::where('pos','=',$id)->get()->first();

        if (!$user){// || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'No information '.$id], 404);
        }

        return response()->json([
            '_id'=>$user->id,
            'username'=>$user->username,
            'fullname'=>$user->fullname,
            'perm_products'=>$user->perm_products,
            'perm_categories'=>$user->perm_categories,
            'perm_transactions'=>$user->perm_transactions,
            'perm_users'=>$user->perm_users,
            'perm_settings'=>$user->perm_settings,
            'status'=>$user->status,
            'password'=>$user->password,
            'pos'=>$user->pos,
            "auth"=> true
            //$user,
        ]);
    }
    public function destroy($id)
    {
        $setting = User::where('pos',$id)->first();

        if (!$setting) {
            return response()->json(['error' => 'User not found'], Response::HTTP_NOT_FOUND);
        }

        $setting->delete();
        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}

