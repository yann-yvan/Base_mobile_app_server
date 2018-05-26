<?php

namespace App\Http\Middleware;

use App\Http\Controllers\Controller;
use App\Http\Response\Code;
use Closure;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenBlacklistedException;
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
        $controller = new Controller();
        try {
            JWTAuth::parseToken()->authenticate();
        } catch (TokenExpiredException $e) {
            return $controller->respond_to_client(Code::$TOKEN_EXPIRED,
                JWTAuth::refresh(JWTAuth::getToken()));
        } catch (TokenBlacklistedException $e) {
            //Black listed Token
            return $controller->respond_to_client(Code::$BLACK_LISTED_TOKEN);
        } catch (TokenInvalidException $e) {
            //Invalid Token
            return $controller->respond_to_client(Code::$INVALID_TOKEN);
        } catch (UserNotDefinedException $e) {
            //user not found
            return $controller->respond_to_client(Code::$USER_NOT_FOUND);
        } catch (JWTException $e) {
            //Token required
            return $controller->respond_to_client(Code::$NO_TOKEN);
        }
        return $next($request);
    }
}
