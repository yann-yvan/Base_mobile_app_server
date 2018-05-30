<?php

namespace App\Http\Controllers\Mobile\Auth;

use App\Http\Controllers\Controller;
use App\Http\Response\Code;
use App\User;
use App\Verification;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\Facades\Image;
use Tymon\JWTAuth\Facades\JWTAuth;

class VerificationController extends Controller
{
    private $sample_login_format = ['message' => "Please send data like in sample",
        'sample' => "{'email' : 'sample@domain'}"];

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('auth.resend_mail_form');
    }

    /**
     * @param $token
     * @return $this
     */
    public function activateByToken($token)
    {
        //get unexpired token
        $result = Verification::where('token', '=', $token)
            ->where('created_at', '>', Carbon::now()->subHours(2))
            ->first();

        //check if code exist
        if (empty($result) || $result === null) {
            //delete confirmation
            Verification::where('token', $token)->delete();
            return view('auth.verification_error')->with(['button' => true, 'message' => 'oh no, the link has expired.']);
        }

        //update account verification status
        User::where('email', $result->email)->update(array("activate" => true));


        //delete confirmation
        Verification::where('token', $result->token)->delete();

        return view('auth.verification_success')->with(['button' => false, 'message' => 'Welcome to ' . env('APP_NAME') . '.']);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function activateByCode(Request $request)
    {

        //decode receive information
        $data = json_decode($request->getContent(), true);
        $credentials = array_get($data, "user");
        $code = array_get($data, "code");

        //check data integrity
        if (!is_array($data) or !array_key_exists("user", $data))
            return $this->respond_to_client(Code::$WRONG_JSON_FORMAT, null, $this->sample_login_format);


        //just for check all input
        $validate = $credentials;
        $validate['code'] = array_get($data, "code");

        $token = null;
        //check if all required information are receive
        $validator = Validator::make($validate, [
            'email' => 'required|email',
            'password' => 'required',
            'code' => 'required'
        ]);

        //return the account already exist
        if ($validator->fails()) {
            return $this->respond_to_client(Code::$MISSING_DATA);
        }


        //get unexpired token
        $result = Verification::where('code', '=', $code)
            ->where('email', '=', $credentials['email'])
            ->where('created_at', '>', Carbon::now()->subHours(2))
            ->first();

        //check if code exist
        if (empty($result) || $result === null) {
            return $this->respond_to_client(Code::$USER_NOT_FOUND);
        }


        //update account verification status
        User::where('email', $result->email)->update(array("activate" => true));

        //delete confirmation
        Verification::where('code', $result->code)->delete();

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

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function resendCode(Request $request)
    {
        //decode receive information
        $data = json_decode($request->getContent(), true);

        //check data integrity
        if (!is_array($data) or !array_key_exists("email", $data))
            return $this->respond_to_client(Code::$WRONG_JSON_FORMAT, null, $this->sample_login_format);


        //make validation
        $validator = Validator::make($data, [
            'email' => 'required|email'
        ]);

        //check if rule respect
        if ($validator->fails()) {
            return $this->respond_to_client(Code::$MISSING_DATA, null, $validator->errors());
        }

        $user = User::where('email', '=', array_get($data, "email"))->first();

        if (empty($user) || $user === null) {
            return $this->respond_to_client(Code::$USER_NOT_FOUND);
        }

        if (boolval($user->activate))
            return $this->respond_to_client(Code::$ACCOUNT_VERIFIED);

        RegisterController::sendVerificationCode(array_get($data, "email"), $user->name);

        return $this->respond_to_client(Code::$SUCCESS);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function resendCodeWeb(Request $request)
    {
        //decode receive information
        $data = ['email' => $request->email];

        //make validation
        $validator = Validator::make($data, [
            'email' => 'required|email'
        ]);

        //check if rule respect
        if ($validator->fails()) {
            return response()->json(['status' => 'fails', 'msg' => $validator->errors()->get('email')]);
        }

        $user = User::where('email', '=', array_get($data, "email"))->first();

        if (empty($user) || $user === null) {
            return response()->json(['status' => 'fails', 'msg' => 'Sorry, user not found']);
        }

        if (boolval($user->activate))
            return response()->json(['status' => 'fails', 'msg' => 'Your account is already verified']);

        RegisterController::sendVerificationCode(array_get($data, "email"), $user->name);

        return response()->json(['status' => 'success']);
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Verification $verification
     * @return \Illuminate\Http\Response
     */
    public function show(Verification $verification)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Verification $verification
     * @return \Illuminate\Http\Response
     */
    public function edit(Verification $verification)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\Verification $verification
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Verification $verification)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Verification $verification
     * @return \Illuminate\Http\Response
     */
    public function destroy(Verification $verification)
    {
        //
    }
}
