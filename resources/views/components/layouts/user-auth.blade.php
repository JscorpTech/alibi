<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ env("APP_NAME") }}</title>
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset("assets/favicon_io/apple-touch-icon.png") }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset("assets/favicon_io/favicon-32x32.png") }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset("assets/favicon_io/favicon-16x16.png") }}">
    <link rel="manifest" href="{{ asset("assets/favicon_io/site.webmanifest") }}">


    <link rel="stylesheet" href="{{asset("output.css")}}">
    @livewireStyles
</head>
<body>
    {{$slot}}

@livewireScripts
</body>
</html>
