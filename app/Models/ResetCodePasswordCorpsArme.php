<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ResetCodePasswordCorpsArme extends Model
{
    protected $fillable = [
        'email',
        'code'
   ];
}
