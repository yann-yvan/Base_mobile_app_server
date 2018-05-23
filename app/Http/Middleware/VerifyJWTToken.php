<?php

namespace App\Http\Middleware;

use Closure;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Exceptions\UserNotDefinedException;
use Tymon\JWTAuth\Facades\JWTAuth;


class VerifyJWTToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        try {

            $user = JWTAuth::parseToken()->authenticate();

        } catch (TokenExpiredException $e) {

            //Token expired
            return response()->json([
                'status' => false,
                'message' => 1,
                //refresh the Token and send it back to user
                'token' => JWTAuth::refresh(JWTAuth::getToken())
            ]);
        } catch (TokenInvalidException $e) {
            //Invalid Token
            return response()->json([
                'status' => false,
                'message' => 2,
            ]);
        } catch (UserNotDefinedException $e) {
            //user not found
            return response()->json([
                'status' => false,
                'message' => 4,
            ]);
        } catch (JWTException $e) {
            //Token required
            return response()->json([
                'status' => false,
                'message' => 3,
            ]);
        }
        return $next($request);
    }
}
