<?php

namespace App\Http\Controllers\Pompiste;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Service;
use App\Models\Personnel;
use App\Models\Soute;
use App\Models\Distributeur;
use App\Models\Carburant;


class PompisteController extends Controller
{
    public function distribution()
    {

        return view('pompiste.services.distribution');
    }

    public function depotage()
    {

        return view('pompiste.services.depotage');
    }
}
