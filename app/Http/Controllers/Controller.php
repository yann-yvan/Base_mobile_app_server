<?php

namespace App\Http\Controllers;

use App\Http\Response\Code;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller as BaseController;
use Tymon\JWTAuth\Facades\JWTAuth;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * Return a success response to the client
     * <blockquote>
     * <pre>
     * {
     *  'status' => true,
     * 'message' => 1500,
     * 'data_name' => $data
     * }
     * </pre></blockquote>
     *
     * @param $message
     * @param null $token
     * @param null $data
     * @param string $data_name
     * @return JsonResponse
     */
    public function respond_to_client($message, $token = null, $data = null, $data_name = "data")
    {
        $code = new Code($message, $data_name);
        $code->setData($data);
        $code->setToken($token);
        return response()->json($code->reply());
    }

    /**
     * Get the user by token.
     *
     * @return mixed
     */
    public function getUser()
    {
        return JWTAuth::parseToken()->authenticate();
    }
}
