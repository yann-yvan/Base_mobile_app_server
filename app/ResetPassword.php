<?php

namespace App;

use Dirape\Token\DirapeToken;
use Illuminate\Database\Eloquent\Model;

class ResetPassword extends Model
{
    use DirapeToken;
    protected $DT_Column = 'token';
    protected $table = 'password_resets';
}
