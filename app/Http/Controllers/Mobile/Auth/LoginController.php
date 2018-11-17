<?php

namespace App\Http\Controllers\Mobile\Auth;

use App\Http\Controllers\Controller;
use App\Http\Response\Code;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\Facades\Image;
use Tymon\JWTAuth\Facades\JWTAuth;


class LoginController extends Controller
{
    private $sample_login_format = ['message' => "Please send data like in sample",
        'sample' => "{'user' : {'email' : sample@domain, 'password' : 12345678}  }"];

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        //decode receive information
        $data = json_decode($request->getContent(), true);
        //check data integrity
        if (!is_array($data) or !array_key_exists("user", $data))
            return $this->respond_to_client(Code::$WRONG_JSON_FORMAT, null, $this->sample_login_format);

        $credentials = array_get($data, "user");

        //make validation
        $validator = Validator::make($credentials, [
            'email' => 'required|email',
            'password' => 'required'
        ]);

        //check if rule respect
        if ($validator->fails()) {
            return $this->respond_to_client(Code::$MISSING_DATA, null, $validator->errors());
        }

        $token = JWTAuth::attempt($credentials);

        if ($token) {
            //get user
            JWTAuth::setToken($token);
            $user = JWTAuth::toUser();

            //check if the account is active
            if (!boolval($user->activate)) {
                return $this->respond_to_client(Code::$ACCOUNT_NOT_VERIFY);
            }

            //prepare image for upload
            if ($user->picture != null) {
                $user->picture = base64_encode(Image::make($user->picture)->encode('png', 50));
            }
            $user->activate = boolval($user->activate);
            //return user information
            return $this->respond_to_client(Code::$SUCCESS, $token, $user);
        } else {
            //check error level
            $user = User::where('email', '=', $credentials['email'])->first();
            if (empty($user) || $user === null) {
                //wrong username
                return $this->respond_to_client(Code::$WRONG_USERNAME);
            } else {
                //wrong password
                return $this->respond_to_client(Code::$WRONG_PASSWORD);
            }
        }
    }

}
