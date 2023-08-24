<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte de Ticket</title>
    @yield('header')
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-secondary">
    <div id="preloader" class="preloader">
        Cargando...
    </div>
    @yield('env')
    @yield('container')
    <script type="text/javascript" language="javascript" src="//cdnjs.cloudflare.com/ajax/libs/jszip/2.5.0/jszip.min.js">
</script>
</body>
</html>