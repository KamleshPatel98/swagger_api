<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use OpenApi\Annotations as OA;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

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
    public function index()
    {
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
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Internal server error!"),
     *             @OA\Property(property="error", type="string", example="Internal server error!")
     *         )
     *     )
     * )
     */
    public function store(Request $request)
    {
        $validated = Validator::make($request->all(), [
            'name' => 'required|string|max:50',
            'email' => 'required|max:50|email|unique:users',
            'password' => 'required|confirmed|min:8|max:20'
        ]);

        if ($validated->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation error!',
                'error' => $validated->errors()->first()
            ], 422);
        }

        try {
            User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);
            return response()->json([
                'status' => true,
                'message' => 'User created successfully!'
            ], 200);
        } catch (\Exception $ex) {
            Log::error('User create error: ' . $ex->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Internal server error!',
                'error' => 'Internal server error!'
            ], 500);
        }
    }

    /**
         * @OA\Get(
         *     path="/api/users/{user}",
         *     summary="Get user profile details",
         *     tags={"users"},
         *     @OA\Parameter(
         *         name="user",
         *         in="path",
         *         required=true,
         *         description="ID of the user to retrieve",
        *         @OA\Schema(type="integer", example=1)
        *     ),
        *     @OA\Response(
        *         response=200,
        *         description="User Profile details",
        *         @OA\JsonContent(
        *             @OA\Property(property="status", type="boolean", example=true),
        *             @OA\Property(property="message", type="string", example="User Profile details!"),
        *             @OA\Property(
        *                 property="data",
        *                 type="object",
        *                 @OA\Property(property="id", type="integer", example=1),
        *                 @OA\Property(property="name", type="string", example="John Doe"),
        *                 @OA\Property(property="email", type="string", example="john@example.com"),
        *                 @OA\Property(property="created_at", type="string", format="date-time", example="2024-01-01T12:00:00Z"),
        *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2024-06-20T10:00:00Z")
        *             )
        *         )
        *     ),
        *     @OA\Response(
        *         response=404,
        *         description="User not found"
        *     )
        * )
    */

    public function show(User $user){
        return response()->json(['status'=>true, 'message'=>'User Profile details!','data'=>$user],200);
    }

    /**
     * @OA\Put(
     *     path="/api/users/{user}",
     *     summary="Update user details",
     *     tags={"users"},
     *     @OA\Parameter(
     *         name="user",
     *         in="path",
     *         required=true,
     *         description="ID of the user to update",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "email"},
     *             @OA\Property(property="name", type="string", example="John Doe", maxLength=50),
     *             @OA\Property(property="email", type="string", format="email", example="john@example.com", maxLength=50)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="User updated successfully!")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Validation error!"),
     *             @OA\Property(property="error", type="string", example="The email has already been taken.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Internal server error!"),
     *             @OA\Property(property="error", type="string", example="Internal server error!")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="User not found"
     *     )
     * )
     */
    public function update(Request $request, User $user)
    {
        $validated = Validator::make($request->all(), [
            'name' => 'required|string|max:50',
            'email' => [
                'required',
                'max:50',
                'email',
                Rule::unique('users')->ignore($user->id)
            ],
        ]);

        if ($validated->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation error!',
                'error' => $validated->errors()->first()
            ], 422);
        }

        try {
            $user->update([
                'name' => $request->name,
                'email' => $request->email,
            ]);
            return response()->json([
                'status' => true,
                'message' => 'User updated successfully!'
            ], 200);
        } catch (\Exception $ex) {
            Log::error('User update error: ' . $ex->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Internal server error!',
                'error' => 'Internal server error!'
            ], 500);
        }
    }

    /**
         * @OA\Delete(
         *     path="/api/users/{user}",
         *     summary="Delete a user",
         *     tags={"users"},
         *     @OA\Parameter(
         *         name="user",
         *         in="path",
         *         required=true,
         *         description="ID of the user to delete",
         *         @OA\Schema(type="integer", example=1)
         *     ),
         *     @OA\Response(
         *         response=200,
         *         description="User deleted successfully",
         *         @OA\JsonContent(
         *             @OA\Property(property="status", type="boolean", example=true),
         *             @OA\Property(property="message", type="string", example="User deleted successfully!")
         *         )
         *     ),
         *     @OA\Response(
         *         response=404,
         *         description="User not found"
         *     )
         * )
    */
    public function destroy(User $user)
    {
        try {
            $user->delete();
            return response()->json([
                'status' => true,
                'message' => 'User deleted successfully!'
            ], 200);
        } catch (\Exception $ex) {
            Log::error('User delete error: ' . $ex->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Internal server error!',
                'error' => 'Internal server error!'
            ], 500);
        }
    }
}