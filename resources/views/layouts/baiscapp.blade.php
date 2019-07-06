<!DOCTYPE html>
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
        <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet" type="text/css">

        <!-- Styles -->
        <link href="{{ asset('css/app.css') }}" rel="stylesheet">

        <style>
            body{
                color: #CD2B42;
                background-color:#FFF;
            }
            
            body a, body a:hover, body a:active{
                color: #CD2B42;
                text-decoration: none;
            }
            .app-link {
                line-height: 20px;
                
            }
            .app-link .link-svg{
                margin-right: 15px;
            }

            ul {
                list-style: none; /* Remove default bullets */
            }

            ul li::before {
                content: "\2022";  /* Add content: \2022 is the CSS Code/unicode for a bullet */
                color: #CD2B42; /* Change the color */
                font-weight: bold; /* If you want it to be bold */
                display: inline-block; /* Needed to add space between the bullet and the text */ 
                width: 1em; /* Also needed for space (tweak if needed) */
                margin-left: -1em; /* Also needed for space (tweak if needed) */
            }
        </style>
    </head>
    <body>
        <div id="app">

            @yield('content')

        </div>
    </body>
</html>
