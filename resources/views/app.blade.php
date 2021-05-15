<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <title>{{ config('app.name', 'Laravel') }}</title>

  {{-- Bootstrap --}}
  <link rel="stylesheet" href="vendor/bootstrap/css/bootstrap.min.css">

  {{-- Font-Awesome --}}
  <!-- <link rel="stylesheet" href="vendor/font-awesome/css/font-awesome.min.css"> -->

  <!-- Styles -->
  <!-- <link rel="stylesheet" href="{{ mix('css/app.css') }}"> -->

  <!-- Scripts -->
  @routes
  <script src="{{ mix('js/app.js') }}" defer></script>
  <script src="vendor/jquery/jquery.min.js" defer></script>
  <script src="vendor/bootstrap/js/bootstrap.min.js" defer></script>
</head>

<body class="font-sans antialiased">
  @inertia
</body>

</html>