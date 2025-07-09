<?php

namespace App\Http\Controllers\ApiController;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;


class AuthController extends Controller
{
    public function getAllUser()
    {
        $users = User::all();
        return response()->json($users, 200);
    }

    public function register(Request $request)
    {
        // ✅ VALIDASI WAJIB untuk vendor
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'nama_usaha' => 'required|string|max:255',
            'alamat' => 'nullable|string|max:255',
            'no_telepon' => 'nullable|string|max:20',
        ]);

        // Kirim error jika validasi gagal
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Buat user baru
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // Buat vendor (tenun) baru yang terhubung ke user
        $vendor = \App\Models\Tenun::create([
            'user_id' => $user->id,
            'nama_usaha' => $request->nama_usaha,
            'alamat' => $request->alamat ?? '-',
            'no_telepon' => $request->no_telepon ?? '-',
        ]);
        // Generate token
        $token = $user->createToken('auth_token')->plainTextToken;

        // Response
        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user,
            'vendor' => $vendor
        ], 201);
    }


    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'Login failed, please check your credentials.',
            ], 401);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Login successful',
            'token' => $token,
            'token_type' => 'Bearer',
            'user_data' => $user,
        ]);
    }


    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        return response()->json([
            'message' => 'Logged out successfully'
        ]);
    }


    // Update ni
    public function update(Request $request)
{
    $user = auth()->user();

    $validator = Validator::make($request->all(), [
        'name' => 'sometimes|required|string|max:255',
        'email' => 'sometimes|required|string|email|max:255|unique:users,email,'.$user->id,
        'password' => 'nullable|string|min:8',
    ]);

    if ($validator->fails()) {
        return response()->json($validator->errors(), 422);
    }

    if ($request->has('password')) {
        $user->password = Hash::make($request->password);
    }

    if ($request->has('name')) {
        $user->name = $request->name;
    }

    if ($request->has('email')) {
        $user->email = $request->email;
    }

    $user->save();

    return response()->json(['message' => 'User updated successfully', 'user' => $user]);
}

}
