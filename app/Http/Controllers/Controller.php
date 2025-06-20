<?php

namespace App\Http\Controllers;
use OpenApi\Annotations as OA;

abstract class Controller
{
    /**
     * @OA\Info(
     *     title="Your API Title",
     *     version="1.0.0",
     *     description="This is the API documentation for Your App"
     * )
     *
     * @OA\Server(
     *     url=L5_SWAGGER_CONST_HOST,
     *     description="API Server"
     * )
     */
}
