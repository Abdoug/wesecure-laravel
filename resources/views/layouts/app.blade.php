<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}" defer></script>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">

    <style>
        /* width */
        ::-webkit-scrollbar {
            width: 7px;
        }

        /* Track */
        ::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

        /* Handle */
        ::-webkit-scrollbar-thumb {
            background: #a7a7a7;
        }

        /* Handle on hover */
        ::-webkit-scrollbar-thumb:hover {
            background: #929292;
        }

        ul {
            margin: 0;
            padding: 0;
        }

        li {
            list-style: none;
        }

        .user-wrapper,
        .message-wrapper {
            border: 1px solid #dddddd;
            overflow-y: auto;
        }

        .user-wrapper {
            height: 600px;
        }

        .user {
            cursor: pointer;
            padding: 5px 0;
            position: relative;
        }

        .user:hover {
            background: #eeeeee;
        }

        .user:last-child {
            margin-bottom: 0;
        }

        .pending {
            position: absolute;
            left: 13px;
            top: 9px;
            background: #b600ff;
            margin: 0;
            border-radius: 50%;
            width: 18px;
            height: 18px;
            line-height: 18px;
            padding-left: 5px;
            color: #ffffff;
            font-size: 12px;
        }

        .media-left {
            margin: 0 10px;
        }

        .media-left img {
            width: 64px;
            border-radius: 64px;
        }

        .media-body p {
            margin: 6px 0;
        }

        .message-wrapper {
            padding: 10px;
            height: 536px;
            background: #eeeeee;
        }

        .messages .message {
            margin-bottom: 15px;
        }

        .messages .message:last-child {
            margin-bottom: 0;
        }

        .received,
        .sent {
            width: 45%;
            padding: 3px 10px;
            border-radius: 10px;
        }

        .received {
            background: #ffffff;
        }

        .sent {
            background: #3bebff;
            float: right;
            text-align: right;
        }

        .message p {
            margin: 5px 0;
        }

        .date {
            color: #777777;
            font-size: 12px;
        }

        .active {
            background: #eeeeee;
        }

        input[type=text] {
            width: 100%;
            padding: 12px 20px;
            margin: 15px 0 0 0;
            display: inline-block;
            border-radius: 4px;
            box-sizing: border-box;
            outline: none;
            border: 1px solid #cccccc;
        }

        input[type=text]:focus {
            border: 1px solid #aaaaaa;
        }
    </style>
    <link href="{{ asset('css/custom.css') }}" rel="stylesheet">

</head>

<body>
<div id="app">
    <nav class="navbar navbar-expand-md shadow-sm navbar-app">
        <div class="container mr-0 container-app">
            <a class="navbar-brand" href="{{ url('/') }}">
                {{ config('app.name', 'Laravel') }}
            </a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false"
                    aria-label="{{ __('Toggle navigation') }}">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <!-- Left Side Of Navbar -->
                <ul class="navbar-nav mr-auto">

                </ul>

                <!-- Right Side Of Navbar -->
                <ul class="navbar-nav ml-auto link-app">
                    <!-- Authentication Links -->
                    @guest
                        <li class="nav-item pl-4">
                            <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                        </li>
                        @if (Route::has('register'))
                            <li class="nav-item pl-4">
                                <a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a>
                            </li>
                        @endif
                    @else
                        <li class="nav-item dropdown">
                            <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                {{ Auth::user()->name }} <span class="caret"></span>
                            </a>

                            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                <a class="dropdown-item" href="{{ route('logout') }}" onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit(); removeStorage();">
                                    {{ __('Logout') }}
                                </a>

                                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                    @csrf
                                </form>
                            </div>
                        </li>
                    @endguest
                </ul>
            </div>
        </div>
    </nav>

    <main class="py-4">
        @yield('content')
    </main>
</div>
</body>

<script src="https://js.pusher.com/6.0/pusher.min.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
{{--<script src="https://cdn.jsdelivr.net/npm/hybrid-crypto-js@0.2.2/web/hybrid-crypto.min.js"></script>--}}
<script src="{{asset('js/hybrid-crypto.min.js')}}"></script>

<script>
    var receiver_id = '';
    var my_id = "{{ Auth::id() }}";
    var crypt = new Crypt();
    var rsa = new RSA({
        keySize: 4096,
        rsaStandard: 'RSAES-PKCS1-V1_5', // RSA-OAEP or RSAES-PKCS1-V1_5,
    });
    let dec2hex = (dec) => {
        return ('0' + dec.toString(16)).substr(-2);
    };
    let generateKey = (len) => {
        var arr = new Uint8Array((len || 40) / 2);
        window.crypto.getRandomValues(arr);
        return Array.from(arr, dec2hex).join('');
    };
    $(document).ready(function () {
        // Enable pusher logging - don't include this in production
        //Pusher.logToConsole = true;
        let public_key_server = localStorage.getItem('public_key_server');
        let private_key = localStorage.getItem('private_key_' + my_id);
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $(`meta[name="csrf-token"]`).attr('content')
            }
        });
        var pusher = new Pusher('3f6a80d2b3906fac1080', {
            cluster: 'eu',
            forceTLS: true
        });
        var channel = pusher.subscribe('my-channel');
        channel.bind('my-event', function (data) {
            data = data.message;
            if (my_id == data.from) {
                $('#' + data.to).click();
            } else if (my_id == data.to) {
                if (receiver_id == data.from) {
                    $('#' + data.from).click();
                } else {
                    var pending = parseInt($('#' + data.from).find('.pending').html());
                    if (pending) {
                        $('#' + data.from).find('.pending').html(pending + 1);
                    } else {
                        $('#' + data.from).append("<span class='pending'>1</span>");
                    }
                }
            }
        });
        $('.user').on('click', function () {
            $('.user').removeClass('active');
            $(this).addClass('active');
            $(this).find('.pending').remove();
            receiver_id = $(this).attr('id');
            $.ajax({
                method: 'get',
                url: 'messages/' + receiver_id,
                data: '',
                cache: false,
                success: function (data) {
                    $('#messages').html(data);
                    scroller();
                }
            })
        });
        $(document).on('keyup', '.input-text input', function (e) {
            var message = $(this).val();
            if (e.keyCode === 13 && message !== '' && receiver_id !== '') {
                let sign = crypt.signature(private_key, message);
                const {signature} = JSON.parse(sign);
                let encrypted = crypt.encrypt(public_key_server, message, signature);
                encrypted = JSON.parse(encrypted);
                $(this).val('');
                console.log("S: ", encrypted, " ", sign);
                let formData = [
                    'receiver_id=' + receiver_id,
                    'encrypted=' + encodeURIComponent(encrypted.cipher),
                    'ckey=' + encodeURIComponent(encrypted.keys[Object.keys(encrypted.keys)[0]]),
                    'iv=' + encodeURIComponent(encrypted.iv),
                    'signature=' + encodeURIComponent(signature)
                ];
                $.ajax({
                    type: 'post',
                    url: 'message',
                    data: formData.join('&'),
                    cache: false,
                    success: (data) => {
                    },
                    error: () => {
                    },
                    complete: () => {
                        scroller();
                    }
                });
            }
        });

        function scroller() {
            let selector = $('.message-wrapper');
            selector.animate({
                scrollTop: selector.get(0).scrollHeight
            }, 50);
        }
    });
</script>
@yield('scripts')
</html>
