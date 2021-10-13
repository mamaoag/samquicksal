<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Restaurant | Login</title>
    <link rel="icon" href="{{ asset('images/samquicksalLogo.png') }}">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('css/restaurant.css') }}">
    <script type="text/javascript" src="{{ asset('js/sweetalert2.all.min.js') }}"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" integrity="sha512-1ycn6IcaQQ40/MKBW2W4Rhis/DbILU74C1vSrLJxCq57o941Ym01SwNsOMqvEBFlcgUa6xLiPY/NS5R+E6ztJQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>
<body class="login font-Montserrat">
    @if (session()->has('passwordUpdated'))
        <script>
            Swal.fire(
                'Your password has been updated',
                'You can now login again',
                'success'
            );
        </script>
    @endif
    <div class="box"></div>

    <div class="form mt-12">
        <header class="flex items-center justify-center mb-10">
           <a href="/" target="_blank"><img class="h-20"src="{{ asset('images/samquicksalLogo.png') }}" alt="samquicksalLogo.png"></a> 
        </header>

        <section class="w-3/6 h-4/6 grid grid-cols-12 mx-auto shadow-xl ">
            <div class="col-span-5 ">
                <div style="background-image: linear-gradient(rgba(255, 255, 255, 0), rgba(255, 255, 255, 0)), url({{asset('images/login-left-bg.png')}}); background-repeat: no-repeat; background-size: cover;" class="h-full rounded-l-xl"></div>
            </div>
            
            <div class="grid items-center w-full py-12 px-16 col-span-7 bg-gradient-to-b from-white to-headerBgColor rounded-r-xl">
                <form action="/restaurant/login" method="POST">
                    @csrf
                    <h1 class="flex justify-center text-login text-2xl text-adminLoginTextColor">Restaurant Login</h1>
                    <div class="grid grid-col-2 my-8 text-sm">
                        <label class="text-loginLandingPage">Username</label>
                        <div class="w-full relative">
                            <i class="fas fa-user absolute px-3 py-3 text-loginLandingPage"></i>
                            <input type="text" name="username" class="w-full pl-8 pr-3 py-2 rounded-md border-multiStepBoxBorder {{($errors->has('username') ? 'border-red-600' : 'border-loginLandingPage')}} focus:outline-none" autocomplete="off">
                        </div>
                        <span class="text-red-600 h-4 mt-1">@error('username'){{ "Username is required" }}@enderror</span>
                        
                        <label class="text-loginLandingPage mt-5">Password</label>
                        <div class="w-full relative">
                            <i class="fas fa-lock absolute px-3 py-3 text-loginLandingPage"></i>
                        <input type="password" name="password" class="w-full pl-8 pr-3 py-2 rounded-md border-multiStepBoxBorder {{($errors->has('password') ? 'border-red-600' : 'border-loginLandingPage')}} focus:outline-none" autocomplete="off"">
                        </div>

                        <span class="text-red-600 h-4 mt-1">
                            @error('password'){{ "Password is required" }}@enderror
                            
                            @if (session()->has('invalidPassword'))
                                <h3>{{ session('invalidPassword') }}</h3>
                            @endif
                        </span>
                        <div class="col-span-full text-right">
                            <a href="/restaurant/login/forgot-password" class="text-sm text-loginLandingPage underline">Forgot Password?</a>
                        </div>
                    </div>
                    
                    <div class="text-center">
                        <button class="text-white bg-headerActiveTextColor rounded-full h-10 w-56 mb-5 text-xs font-Roboto hover:bg-btnHoverColor  hover:shadow-xl transition duration-300 ease-in-out hover:font-bold">Login</button>
                    </div>
                </form>
            </div>
        </section>
    </div>
</body>
</html>