<!DOCTYPE html>
<html>
    <head>
        <title>@yield('title')</title>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="/css/uikit.min.css" />
        <script src="/js/uikit.min.js"></script>
    </head>
    <body>
        <div class="uk-container">
            @yield('content')
        </div>
    </body>
</html>