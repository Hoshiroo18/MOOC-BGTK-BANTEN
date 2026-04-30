<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <title>@yield('title', 'MOOC BGTK Banten')</title>

  <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
  <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
  @stack('styles')
</head>
<body>

  @include('partials.header')

  <main class="main">
    @yield('content')
  </main>

  @include('partials.footer')

</body>
</html>