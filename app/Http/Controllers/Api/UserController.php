<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use OpenApi\Annotations as OA;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
        * @OA\Get(
        *     path="/api/users",
        *     summary="Get list of users",
        *     tags={"users"},
        *     @OA\Response(
        *         response=200,
        *         description="Successful response"
        *     )
        * )
    */
    public function index(){
        return User::all();
    }

    /**
     * @OA\Post(
     *     path="/api/users",
     *     summary="Register a new user",
     *     tags={"users"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name","email","password","password_confirmation"},
     *             @OA\Property(property="name", type="string", example="John Doe", maxLength=50),
    *             @OA\Property(property="email", type="string", format="email", example="john@example.com", maxLength=50),
    *             @OA\Property(property="password", type="string", format="password", example="secret123", minLength=8, maxLength=20),
    *             @OA\Property(property="password_confirmation", type="string", format="password", example="secret123")
    *         )
    *     ),
    *     @OA\Response(
    *         response=200,
    *         description="User created successfully",
    *         @OA\JsonContent(
    *             @OA\Property(property="status", type="boolean", example=true),
    *             @OA\Property(property="message", type="string", example="User created successfully!")
    *         )
    *     ),
    *     @OA\Response(
    *         response=422,
    *         description="Validation error",
    *         @OA\JsonContent(
    *             @OA\Property(property="status", type="boolean", example=false),
    *             @OA\Property(property="message", type="string", example="Validation error!"),
    *             @OA\Property(property="error", type="string", example="The email field is required.")
    *         )
    *     )
    * )
    */

    public function store(Request $request){
        $validated = Validator::make($request->all(),[
            'name' => 'required|string|max:50',
            'email' => 'required|max:50|email|unique:users',
            'password' => 'required|confirmed|min:8|max:20'
        ]);
        if($validated->fails()){
            return response()->json(['status'=>false, 'message'=>'Validation error!', 'error'=>$validated->errors()->first()], 422);
        }

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);
        return response()->json(['status'=>true, 'message'=>'User created successfully!'], 200);
    }
}
