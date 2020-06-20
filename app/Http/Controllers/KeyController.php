<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use phpseclib\Crypt\RSA;
use phpseclib\Crypt\RSA\Formats\Keys\PKCS1;
use phpseclib\Crypt\RSA\PrivateKey;
use phpseclib\Crypt\RSA\PublicKey;
use App\Key;
use App\Config;
use Illuminate\Support\Facades\Auth;

class KeyController extends Controller
{
    public function store(Request $request)
    {
        $public_key_server = Config::where('key', 'public_key')->first()->value;
        $public_key = $request->public_key;
        $user = Auth::user();
        $matches = ['user_id' => $user->id];
        $key = Key::firstOrNew($matches);
        $key->public_key = $public_key;
        $key->date = Carbon::now();
        $key->save();
        return response()->json(['status' => 200, 'public_key_server' => $public_key_server]);
//        $rsa = new RSA();
//        $privatekey = $rsa->createKey(4096);
//        dd($privatekey["publickey"]);
//        $publickey = $privatekey->getPublicKey();
//        $rsa->loadKey($pub_key);
//        $data = 'Your String';
//        $output = $rsa->encrypt($data);
//        $rsa->loadKey($prv_key);
//        dd($rsa->decrypt($output));
        //$m = $rsa->decrypt($output);
        //echo ($m);
    }
}
