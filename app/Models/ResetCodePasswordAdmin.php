<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ResetCodePasswordAdmin extends Model
{
   protected $fillable = [
        'email',
        'code'
   ];
}
