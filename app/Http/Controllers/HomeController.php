<?php

namespace App\Http\Controllers;

use App\Config;
use App\Events\MyEvent;
use App\Message;
use Exception;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Pusher\Pusher;
use phpseclib\Crypt\RSA;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public
    function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return Renderable
     */
    public
    function index()
    {

        // Select all users except the logged in
        $users = DB::select("select users.id, users.name, users.avatar, users.email, count(is_read) AS unread FROM users LEFT JOIN messages
        ON users.id = messages.from and is_read = 0 and messages.to = " . Auth::id() . " WHERE users.id != " . Auth::id() . " GROUP BY
        users.id, users.name, users.avatar, users.email");

        return view('home', compact('users'));
    }

    public
    function messages($user_id)
    {

        $current_user = Auth::id();

        Message::where(['from' => $user_id, 'to' => $current_user])->update(['is_read' => 1]);

        // Get the messages where from === Auth::id() && to === $user_id OR from === $user_id && to === Auth::id()
        $messages = Message::where(function ($query) use ($current_user, $user_id) {
            $query->where('from', $current_user)->where('to', $user_id);
        })->oRwhere(function ($query) use ($current_user, $user_id) {
            $query->where('from', $user_id)->where('to', $current_user);
        })->get();

        return view('messages.index', compact('messages'));
    }

    public
    function sendMessage(Request $request)
    {

        $user = Auth::user();
        $to = $request->receiver_id;
        $message = $request->message;
        $is_read = 0;
        $private_server = Config::where('key', 'private_key')->first()->value;

        try {

            $rsa = new RSA();
            $rsa->loadKey($private_server);
            $key = $rsa->decrypt(base64_decode($request->post('ckey')));
            $iv = base64_decode($request->post('iv'));
            $encrypted = $request->post('encrypted');
            $plaintext = openssl_decrypt($encrypted, 'AES-256-CBC', $key, OPENSSL_PKCS1_OAEP_PADDING, $iv);
            dd($plaintext);

        } catch (Exception $e) {
            dd($e);
        }
        //$m = $rsa->decrypt($output);

        //dd($request->encrypted, $request->key, hex2bin($request->iv));
        //$ciphertext = openssl_decrypt($request->encrypted, 'AES-128-CBC', $request->key, OPENSSL_RAW_DATA, hex2bin($request->iv));
        //dd($ciphertext);

//        $encrypted = json_decode($request->encrypted);
//        $private_server = Config::where('key', 'private_key')->first()->value;
//        $key = array_values(get_object_vars($encrypted->keys))[0];
//        $iv = $encrypted->iv;
//
//        $cipher = $encrypted->cipher;
//        dd(openssl_decrypt($cipher, 'AES-256-CBC', $key, [OPENSSL_RAW_DATA, OPENSSL_NO_PADDING], $iv));
//        $rsa = new RSA();
//        $rsa->loadKey($private_server);
//
//        try {
//            dd($key);
//            $output = $rsa->decrypt(base64_decode($key));
//            dd($output);
//        }
//        catch (\Exception $e) {
//            dd($e);
//        }
        //dd($output);

        $new_message = new Message();
        $new_message->from = $from;
        $new_message->to = $to;
        $new_message->message = $message;
        $new_message->is_read = $is_read;

        $new_message->save();

        $options = array(
            'cluster' => 'eu',
            'useTls' => true
        );

        $pusher = new Pusher(
            env('PUSHER_APP_KEY'),
            env('PUSHER_APP_SECRET'),
            env('PUSHER_APP_ID'),
            $options
        );

        try {
            $data = ['from' => $from, 'to' => $to];
            event(new MyEvent($data));
            //$pusher->trigger('my-channel', 'my-event', $data);
        } catch (Exception $e) {
            dd($e);
        }
    }
}
