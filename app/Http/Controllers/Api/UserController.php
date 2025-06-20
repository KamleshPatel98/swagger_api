<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use OpenApi\Annotations as OA;

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
}
