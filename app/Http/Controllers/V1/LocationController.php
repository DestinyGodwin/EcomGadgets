<?php

namespace App\Http\Controllers\V1;

use App\Models\State;
use App\Http\Controllers\Controller;
use App\Http\Resources\V1\LgaResource;
use App\Http\Resources\V1\StateResource;

class LocationController extends Controller
{
     public function getStates(){
       $states= State::all();
       return  StateResource::collection($states);
    }

    public function getStateLgas(State $state){
        return  LgaResource::collection($state->lgas);
    }
}
