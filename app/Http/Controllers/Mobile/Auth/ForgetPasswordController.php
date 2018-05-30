<?php

namespace App\Http\Controllers\Mobile\Auth;

use App\Http\Controllers\Controller;
use App\Http\Response\Code;
use App\Mail\SendVerificationData;
use App\ResetPassword;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class ForgetPasswordController extends Controller
{
    public function sendResetCode(Request $request)
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

        if (!self::sendMail($user->email, $user->name))
            return $this->respond_to_client(Code::$FAILURE);

        return $this->respond_to_client(Code::$SUCCESS);
    }

    /**
     * @param $email
     * @param $name
     * @return bool
     */
    public static function sendMail($email, $name)
    {
        $resetPassword = new ResetPassword();
        $resetPassword->email = $email;
        $resetPassword->setToken(DT_RandomNum, 6, false, 'code');
        $resetPassword->setToken(DT_UniqueStr, 100, false, 'token');

        ResetPassword::where('email', 'like', $email)->delete();

        //build verification link
        $url = url(action('Mobile\Auth\ResetPasswordController@index', [$resetPassword->token]));

        $context = "Please use the code on the mobile app or click on the link to reset your password";
        //send email to the user address with all generated code below
        Mail::to($email)->later(5, new SendVerificationData($resetPassword->code, $url, $name, $context, 'Forgot password', 'Reset password'));

        //save user generated code for account activation
        return $resetPassword->save();

    }
}
