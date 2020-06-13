<?php

namespace App\Http\Controllers;

use App\Message;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Pusher\Pusher;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {

        // Select all users except the logged in
        $users = DB::select("select users.id, users.name, users.avatar, users.email, count(is_read) AS unread FROM users LEFT JOIN messages
        ON users.id = messages.from and is_read = 0 and messages.to = " . Auth::id() . " WHERE users.id != " . Auth::id() . " GROUP BY
        users.id, users.name, users.avatar, users.email");

        return view('home', compact('users'));
    }

    public function messages($user_id)
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

    public function sendMessage(Request $request)
    {
        $from = Auth::id();
        $to = $request->receiver_id;
        $message = $request->message;
        $is_read = 0;

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

        $data = ['from' => $from, 'to' => $to];
        $pusher->trigger('my-channel', 'my-event', $data);
    }
}
