<?php

namespace App\Http\Controllers\Mobile;

use App\Http\Controllers\Controller;
use App\Http\Response\Code;

class AuthTestController extends Controller
{
    public function greet()
    {
        $this->respond_to_client(Code::$FAILURE);
    }
}
