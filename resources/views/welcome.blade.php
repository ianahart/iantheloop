<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>iaNtheLoop</title>
    <link rel="stylesheet" href="{{ mix('css/app.css') }}" type="text/css" />
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">
</head>

<body class="antialiased">
    <div id="app">
        <App></App>
    </div>
    <script src="{{ mix('js/app.js') }}"></script>
</body>

</html>