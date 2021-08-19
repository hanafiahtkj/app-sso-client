<x-guest-layout>
    <div id="card-login" style="display:show">
    <x-auth-card>
        <x-slot name="logo">
            <a href="/">
                <x-application-logo class="w-20 h-20 fill-current text-gray-500" />
            </a>
        </x-slot>

        <!-- Session Status -->
        <x-auth-session-status class="mb-4" :status="session('status')" />

        <!-- Validation Errors -->
        <x-auth-validation-errors class="mb-4" :errors="$errors" />

        <form id="form-login" method="POST" action="{{ route('login') }}">
            @csrf

            <!-- Email Address -->
            <div>
                <x-label for="email" :value="__('Email')" />

                <x-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus />
            </div>

            <!-- Password -->
            <div class="mt-4">
                <x-label for="password" :value="__('Password')" />

                <x-input id="password" class="block mt-1 w-full"
                                type="password"
                                name="password"
                                required autocomplete="current-password" />
            </div>

            <!-- Remember Me -->
            <div class="block mt-4">
                <label for="remember_me" class="inline-flex items-center">
                    <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" name="remember">
                    <span class="ml-2 text-sm text-gray-600">{{ __('Remember me') }}</span>
                </label>
            </div>

            <div class="flex items-center justify-end mt-4">
                @if (Route::has('password.request'))
                    <a class="underline text-sm text-gray-600 hover:text-gray-900" href="{{ route('password.request') }}">
                        {{ __('Forgot your password?') }}
                    </a>
                @endif

                <button type="button" class="ml-3 btn-sso" onClick="clickLogin();">
                    {{ __('Login SSO') }}
                </button>

                <x-button class="ml-3">
                    {{ __('Log in') }}
                </x-button>

                
            </div>
        </form>
    </x-auth-card>
    </div>

    <div id="card-register" style="display:none">
    <x-auth-card>
        <x-slot name="logo">
            <a href="/">
                <x-application-logo class="w-20 h-20 fill-current text-gray-500" />
            </a>
        </x-slot>

        <!-- Validation Errors -->
        <x-auth-validation-errors class="mb-4" :errors="$errors" />

        <form id="form-register" method="POST" action="{{ route('register') }}">
            @csrf

            <!-- Nik -->
            <div>
                <x-label for="r-nik" :value="__('Nik')" />

                <x-input id="r-nik" class="block mt-1 w-full" type="text" name="nik" :value="old('nik')" required autofocus />
            </div>

            <!-- Name -->
            <div class="mt-4">
                <x-label for="r-name" :value="__('Name')" />

                <x-input id="r-name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus />
            </div>

            <!-- Email Address -->
            <div class="mt-4">
                <x-label for="r-email" :value="__('Email')" />

                <x-input id="r-email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required />
            </div>

            <div class="flex items-center justify-end mt-4">
                <input type="hidden" id="id_sso" name="id_sso">
                <x-button class="ml-4" type="button" onClick="clickRegister();">
                    {{ __('Register / Login') }}
                </x-button>
            </div>
        </form>
    </x-auth-card>
    </div>

    <script src="{{ url('http://sso.banjarmasinkota.test:8000/vendor/bjm-sso/bjm-sso.js') }}"></script>
    <!-- The user is authenticated... -->
    @if(Auth::check())
    <script>
    $(function() { 
        window.location.replace("{{ url('/') }}");
    });
    </script>
    @else
    <!-- The user is not authenticated... -->
    <script>

    async function isRegister(id_sso) {
        const response = await axios.get("{{ url('/sso/is-register') }}" + "/" + id_sso,{withCredentials: true});
        return response.data;
    }

    async function clickLogin() {
        var sso = new BjmSSO();
        sso.loginWindow(beforeLoginToServer);
    }

    function clickRegister() {
        console.log('Click Register');
        sendRegisterToServer();
    }

    async function beforeLoginToServer(result) {
        console.log(result);
        if (result['status']) {
            console.log(result);
            if (result['status']) {
                const isRegister = await self.isRegister(result['data']['id']);
                if (isRegister['status']) {
                    sendLoginToServer(result);
                }
                else {
                    $('#card-login').hide();
                    $('#card-register').show();
                    $('#id_sso').val(result['data']['id']);
                }
            }
        }
    }

    function sendLoginToServer(result) {
        var user = result['data'];
        var formData = new FormData();
        formData.append('_token', '{{ csrf_token() }}');
        formData.append('name', user['name']);
        formData.append('email', user['email']);
        formData.append('id_sso', user['id']);
        $.ajax({
            type: "POST",
            url: "{{ route('sso.register') }}",
            data: formData,
            processData: false,
            contentType: false,
            dataType: "json",
            success: function(data, textStatus, jqXHR) {
                if (data['status'] == true) {
                    window.location.replace("{{ url('/') }}");
                }   
            },
            error: function(data, textStatus, jqXHR) {
                console.log(data);
                console.log('Login Gagal!');
            },
        });
    }

    function sendRegisterToServer() {
        var form = $('#form-register')[0];
        var formData = new FormData(form);
        $.ajax({
            type: "POST",
            url: "{{ route('sso.register') }}",
            data: formData,
            processData: false,
            contentType: false,
            dataType: "json",
            success: function(data, textStatus, jqXHR) {
                if (data['status'] == true) {
                    window.location.replace("{{ url('/') }}");
                }   
            },
            error: function(data, textStatus, jqXHR) {
                console.log(data);
                console.log('Login Gagal!');
            },
        });
    }

    $(function() { 
        @if ($isauto)
        var sso = new BjmSSO();
        sso.login(beforeLoginToServer);
        @endif
    });
    </script>
    @endif

</x-guest-layout> 
