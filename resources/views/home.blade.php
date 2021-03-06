@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-4">
            <div class="user-wrapper">
                <ul class="users">
                    @foreach ( $users as $user )
                    <li class="user" id="{{ $user->id }}">

                        @if ( $user->unread )
                        <span class="pending">{{ $user->unread }}</span>
                        @endif

                        <div class="media">
                            <div class="media-left">
                                <img src="{{ $user->avatar }}" alt="" class="media-object">
                            </div>

                            <div class="media-body">
                                <p class="name">{{ $user->name }}</p>
                                <p class="email">{{ $user->email }}</p>
                            </div>
                        </div>
                    </li>
                    @endforeach
                </ul>
            </div>
        </div>

        <div class="col-md-8" id="messages">

        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    @parent()
    let removeStorage = () => {
        let storage = JSON.parse(JSON.stringify(localStorage));
        let dataToBeRemoved = Object.keys(storage).filter((e, i) => e !== "pusherTransportTLS");
        dataToBeRemoved.map((e, i) => {
           localStorage.removeItem(e);
        });
    };
    let generateAndStoreKeys = () => {
            // Generate RSA key pair, default key size is 4096 bit
            rsa.generateKeyPair(function(keyPair) {
                // Callback function receives new key pair as a first argument
                var publicKey = keyPair.publicKey;
                var privateKey = keyPair.privateKey;
                removeStorage();
                localStorage.setItem("private_key_" + my_id, privateKey);
                localStorage.setItem("public_key_" + my_id, publicKey);
                $.ajax({
                    type: 'post',
                    url: "{{route('keys.store')}}",
                    data: {
                        "public_key": publicKey
                    },
                    cache: false,
                    success: (data) => {
                        if (data.status === 200 && data.public_key_server) {
                            let public_key_server = data.public_key_server;
                            localStorage.setItem("public_key_server", public_key_server);
                            setKeys();
                        }
                    },
                    error: () => {

                    },
                    complete: () => {
                    }
                });
            });
        };
    let setKeys = () => {
        public_key_server = localStorage.getItem('public_key_server');
        private_key = localStorage.getItem('private_key_' + my_id);
    };
    $(document).ready(() => {
        generateAndStoreKeys();
    });
</script>
@endsection