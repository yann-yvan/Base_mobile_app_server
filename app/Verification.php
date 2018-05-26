<?php

namespace App;

use Dirape\Token\DirapeToken;
use Illuminate\Database\Eloquent\Model;

class Verification extends Model
{
    use DirapeToken;
    protected $DT_Column = 'token';
}
