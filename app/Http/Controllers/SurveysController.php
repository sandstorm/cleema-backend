<?php

namespace App\Http\Controllers;

use App\Http\Resources\SurveysCollection;
use App\Models\Surveys;
use App\Models\UpUsers;
use Illuminate\Support\Facades\Auth;

class SurveysController extends Controller
{
    public function fetch ()
    {
        return new SurveysCollection(Surveys::where('finished','=', 'false')->get());
    }

    // !!!surveys are currently just links!!!
    /*public function respond()
    {
        $user = Auth::guard('localAuth')->user();
        if(!$user){
            return response()->json(Controller::getApiErrorMessage('Authentication failed.'), 400);
        }
        $response = request()->input('data');
        if(!$response){
            return response()->json(Controller::getApiErrorMessage('Data missing.'), 400);
        }
        assert($user instanceof UpUsers);
    }*/

    // participate
}
