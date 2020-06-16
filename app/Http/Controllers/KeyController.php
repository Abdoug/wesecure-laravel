<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Pikirasa\RSA;

class KeyController extends Controller
{
    public function store(Request $request)
    {
        $rsa = new \Pikirasa\RSA(null, null);
        $rsa->create();  // creates new keys as strings
        dd($rsa->getPublicKeyFile());
        //dd($request->all());
    }
}
