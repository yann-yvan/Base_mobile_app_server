<?php

namespace App\Http\Controllers\Mobile\Auth;

use App\Http\Controllers\Controller;
use App\Http\Response\Code;
use App\ResetPassword;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ResetPasswordController extends Controller
{

    private $sample_login_format = ['message' => "Please send data like in sample",
        'sample' => "{'code' : 123456, 'user':{'email' : 'sample@domain', 'password' : 12345678, 'password_confirmation' : 12345678}}"];

    /**
     * Display a listing of the resource.
     *
     * @param $token
     * @return \Illuminate\Http\Response
     * @internal param Request $request
     */
    public function index($token)
    {
        //get unexpired token
        $result = ResetPassword::where('token', '=', $token)
            ->where('created_at', '>', Carbon::now()->subHours(2))
            ->first();

        //check if code exist
        if (empty($result) || $result === null) {
            //delete confirmation
            ResetPassword::where('token', $token)->delete();
            return view('auth.verification_error')->with(['button' => true, 'message' => 'oh no, the link has expired.']);
        }
        return view('auth.reset_password_form')->with(['token' => $token]);
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
        //get unexpired token
        $result = ResetPassword::where('token', '=', $request->get('token'))
            ->where('created_at', '>', Carbon::now()->subHours(2))
            ->first();

        //check if code exist
        if (empty($result) || $result === null) {
            //delete confirmation
            ResetPassword::where('token', $request->get('token'))->delete();
            return response()->json(['status' => 'fails', 'msg' => 'Sorry, link expired']);
        }

        $data['password'] = $request->password;
        $data['password_confirmation'] = $request->password_confirmation;

        //make validation
        $validator = Validator::make($data, [
            'password' => 'bail|required|confirmed|min:8'
        ]);

        //check if rule respect
        if ($validator->fails()) {
            return response()->json(['status' => 'fails', 'msg' => $validator->errors()->get('password')]);
        }

        //update account verification status
        User::where('email', $result->email)->update(array("password" => bcrypt($request->password)));

        //delete confirmation
        ResetPassword::where('token', $result->token)->delete();

        return response()->json(['status' => 'success']);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function resetByCode(Request $request)
    {
        //decode receive information
        $data = json_decode($request->getContent(), true);

        //check data integrity
        if (!is_array($data) or !array_key_exists('user', $data))
            return $this->respond_to_client(Code::$WRONG_JSON_FORMAT, null, $this->sample_login_format);

        $user = array_get($data, 'user');
        //get unexpired token
        $result = ResetPassword::where('code', '=', array_get($data, 'code'))
            ->where('email', '=', array_get($user, 'email'))
            ->where('created_at', '>', Carbon::now()->subHours(2))
            ->first();

        //check if code exist
        if (empty($result) || $result === null) {
            //delete confirmation
            ResetPassword::where('email', array_get($user, 'email'))->delete();
            return $this->respond_to_client(Code::$EXPIRED);
        }

        //make validation
        $validator = Validator::make($user, [
            'password' => 'bail|required|confirmed|min:8'
        ]);

        //check if rule respect
        if ($validator->fails()) {
            return $this->respond_to_client(Code::$FAILURE, null, $validator->errors());
        }

        //update account verification status
        User::where('email', $result->email)->update(array("password" => bcrypt(array_get($user, 'password'))));

        //delete confirmation
        ResetPassword::where('email', $result->email)->delete();

        return $this->respond_to_client(Code::$SUCCESS);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\ResetPassword $resetPassword
     * @return \Illuminate\Http\Response
     */
    public function show(ResetPassword $resetPassword)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\ResetPassword $resetPassword
     * @return \Illuminate\Http\Response
     */
    public function edit(ResetPassword $resetPassword)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\ResetPassword $resetPassword
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ResetPassword $resetPassword)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\ResetPassword $resetPassword
     * @return \Illuminate\Http\Response
     */
    public function destroy(ResetPassword $resetPassword)
    {
        //
    }
}
