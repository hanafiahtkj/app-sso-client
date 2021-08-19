<x-guest-layout>
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

        <form method="POST" action="{{ route('login') }}">
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
    function clickLogin() {
        var sso = new BjmSSO();
        sso.loginWindow(function(result) {
            console.log(result);
            if (result['status']) {
                sendToServer(result);
            }
        });
    }

    function sendToServer(result) {
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
                // $(".is-invalid").removeClass("is-invalid");
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
        sso.login(function(result) {
            console.log(result);
            if (result['status']) {
                sendToServer(result);
            }
        });
        @endif
    });
    </script>
    @endif
</x-guest-layout> 