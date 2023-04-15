<?php

namespace App\Http\Controllers;

use App\Http\Requests\user\CreateUserRequest;
use App\Http\Requests\user\LoginRequest;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function __construct(
        private User $user
    ){}

    public function index(){
        return $this->user->get();
    }

    public function store(CreateUserRequest $request){
        $request = $request->validated();
        $user =  $this->user->create([
            'username' => $request['username'],
            'full_name' => $request['full_name'],
            'password' => bcrypt($request['password']),
        ]);

        return response()->json(['message' => env('MESSAGE_SUCCESS'), 'data' => $user], 201);
    }

    public function login(LoginRequest $request){
        $request = $request->validated();
        $user = $this->user->where('username', $request['username'])->first();
        if (!$user) {
            return response()->json(['message' => "Foydalanuvchi yoki parol noto'g'ri kirtilgan"], 400);
        }
        if (!Hash::check($request['password'], $user->password)) return response()->json(['message' => "Foydalanuvchi yoki parol noto'g'ri kirtilgan"], 400);

        return response()->json([
            'status' => true,
            'message' => 'User Logged In Successfully',
            'token' => $user->createToken("API TOKEN")->plainTextToken
        ], 200);
    }

    public function update($id, CreateUserRequest $request){
        $request = $request->validated();
        $user = $this->user->find($id);
        $user->update([
            'username' => $request['username'],
            'full_name' => $request['full_name'],
            'password' => bcrypt($request['password']),
        ]);

        return response()->json(['message' => env('MESSAGE_SUCCESS'), 'data' => $user], 201);
    }
    public function auth(){
        return $this->user->where('id', auth()->id())->first();
    }

}
