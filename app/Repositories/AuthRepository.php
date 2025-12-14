<?php

namespace App\Repositories;

use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthRepository
{
    public function register(array $data)
    {
        $user = User::create
        ([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'photo' => $data['photo'],
            'gender' => $data['gender'],
            'password' => Hash::make($data['password']),
        ]);

        $user->assignRole('customer');
        return $user->load('roles');
    }

    public function login(array $data)
    {
        $credentials = [
            'email' => $data['email'],
            'password' => $data['password']
        ];

        if (!Auth::attempt($credentials))
        {
            return response()->json([
                'message' => 'The provided credentials do not match out records'
            ], 401);
        }

        // ILMU
        //
        // regenerate() disini berfungsi sebagai mencegah adanya session login
        // dengan session lama yang tidak dapat dipakai lagi oleh laravel
        // alias token lama tidak dapat digunakan kembali.
        //
        // jadi token yang aktif digunakan itu bersifat sementara, kamu bisa ubah
        // melalui config/session.php ya! pada bagian lifetime
        request()->session()->regenerate();

        $user = Auth::user();

        return response()->json([
            'message' => 'Login Successful!',
            'user' => new UserResource($user->load('roles'))
        ]);
    }


    // ILMU
    // tokenLogin disini dibuat untuk melakukan API Endpoint di testing
    public function tokenLogin(array $data)
    {
        if(!Auth::attempt([
            'email' => $data['email'],
            'password' => $data['password']
        ])) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        $user = Auth::user();
        $token = $user->createToken('API Token')->plainTextToken;
        // createToken disini sudah dibuat oleh sanctum laravel ya

        return response()->json([
            'message' => 'Login Successful!',
            'token' => $token,
            'user' => new UserResource($user->load('roles'))
        ]);
    }
}
