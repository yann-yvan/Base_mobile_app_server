<?php

namespace App\Http\Controllers\Mobile\Auth;

use App\Http\Controllers\Controller;
use App\Http\Response\Code;
use App\Mail\SendVerificationData;
use App\User;
use App\Verification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{
    private $sample_login_format = ['message' => "Please send data like in sample",
        'sample' => "{'user' : {'email' : sample@domain, 'password' : 12345678}  }"];

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request)
    {
        //decode receive information
        $data = json_decode($request->getContent(), true);

        //check data integrity
        if (!is_array($data))
            return $this->respond_to_client(Code::$WRONG_JSON_FORMAT, null, $this->sample_login_format);


        //check if all required information are receive
        $validator = Validator::make($data, [
            'email' => 'required|email|unique:users',
            'password' => 'required'
        ]);

        //return the account already exist
        if ($validator->fails()) {
            return $this->respond_to_client(Code::$MISSING_DATA, null, $validator->errors());
        }

        $user = new User();

        //send use activation account code and return generated code
        $code_send_successfully = $this->sendVerificationCode(array_get($data, "email"), array_get($data, "name"));

        //manage the case if user has send a profile picture
        if (array_key_exists('picture', $data) and array_get($data, "picture") != null) {
            //save image in picture folder and save path in the database
            $base = base64_decode(array_get($data, "picture"));
            $file_extension = "png";//explode('/', $profile->mime);
            $final_file_path = 'image/profile/profile_' . time() . '.' . $file_extension;
            file_put_contents($final_file_path, $base);
            $user->picture = $final_file_path;
        }

        //prepare all receive information for a database insertion
        $user->name = array_get($data, "name");
        $user->email = array_get($data, "email");
        $user->password = Hash::make(array_get($data, "password"));
        $success = $user->save();

        //check if the registration has succeed
        if (!$success)
            return $this->respond_to_client(Code::$FAILURE);


        //check if the registration has succeed
        if (!$code_send_successfully) {
            //newly account delete
            $user->delete();
            return $this->respond_to_client(Code::$FAILURE);
        }

        return $this->respond_to_client(Code::$SUCCESS);

    }

    /**
     * @param $email
     * @param $name
     * @return bool
     */
    public static function sendVerificationCode($email, $name)
    {
        $verification = new Verification();
        $verification->email = $email;
        $verification->setToken(DT_RandomNum, 6, false, 'code');
        $verification->setToken(DT_UniqueStr, 100, false, 'token');

        Verification::where('email', 'like', $email)->delete();

        //build verification link
        $url = url(action('Mobile\Auth\VerificationController@activateByToken', [$verification->token]));

        $context = "Please verify that your
                            email address is " . $email . ", and that you entered it when signing up for" . env('APP_NAME');
        //send email to the user address with all generated code below
        Mail::to($email)->later(10, new SendVerificationData($verification->code, $url, $name, $context));

        //save user generated code for account activation
        return $verification->save();

    }
}
