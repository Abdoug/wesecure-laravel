<?php

use Illuminate\Database\Seeder;
use App\Config;
use phpseclib\Crypt\RSA;

class ConfigSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $rsa = new RSA();

        $keys = $rsa->createKey(4096);
        $private_key = $keys["privatekey"];
        $public_key = $keys["publickey"];

        $privateKeyConfig = new Config();
        $privateKeyConfig->key = "private_key";
        $privateKeyConfig->value = $private_key;
        $privateKeyConfig->save();
        $publicKeyConfig = new Config();
        $publicKeyConfig->key = "public_key";
        $publicKeyConfig->value = $public_key;
        $publicKeyConfig->save();
    }
}
