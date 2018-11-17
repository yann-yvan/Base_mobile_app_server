<?php
/**
 * Created by PhpStorm.
 * User: yann-yvan
 * Date: 23/05/18
 * Time: 15:48
 */

namespace App\Http\Response;

class Code
{

    /*
     * Token message code
     */
    public static $TOKEN_EXPIRED = -1;
    public static $BLACK_LISTED_TOKEN = -2;
    public static $INVALID_TOKEN = -3;
    public static $NO_TOKEN = -4;
    public static $USER_NOT_FOUND = -5;

    /*
     * Request error message code
     */
    public static $WRONG_JSON_FORMAT = -6;

    /*
    * Common request message code
    */
    public static $SUCCESS = 1000;
    public static $FAILURE = -1001;
    public static $MISSING_DATA = -1002;
    public static $EXPIRED = -1003;
    public static $DATA_EXIST = -1004;

    /*
     * Authentication message code
     */
    public static $ACCOUNT_NOT_VERIFY = -1100;
    public static $WRONG_USERNAME = -1101;
    public static $WRONG_PASSWORD = -1102;
    public static $WRONG_CREDENTIALS = -1103;
    public static $ACCOUNT_VERIFIED = 1104;


    /*
     * Class properties
     */
    private $message = 0;
    private $status = false;
    private $data = null;
    private $data_name = "data";
    private $token = null;

    /**
     * Code constructor.
     * @param int $message
     * @param $name
     */
    public function __construct($message, $name)
    {
        $this->status = $message >= 0;
        $this->message = ($message < 0 ? $message * -1 : $message);
        $this->data_name = ($message < 0 ? 'error' : $name);
    }

    /**
     * @param null $data
     */
    public function setData($data)
    {
        $this->data = $data;
    }

    /**
     * @param null $token
     */
    public function setToken($token)
    {
        $this->token = $token;
    }

    /**
     * @return array
     */
    public function reply()
    {
        if ($this->data == null)
            if ($this->token == null)
                return [
                    'status' => $this->status,
                    'message' => $this->message
                ];
            else
                return [
                    'status' => $this->status,
                    'message' => $this->message,
                    'token' => $this->token
                ];
        else
            if ($this->token == null)
                return [
                    'status' => $this->status,
                    'message' => $this->message,
                    $this->data_name => $this->data
                ];
            else// c est a sa que ressemeble mxon api de repinse serveur
                return [
                    'status' => $this->status,
                    'message' => $this->message,
                    'token' => $this->token,
                    $this->data_name => $this->data
                ];
    }


}