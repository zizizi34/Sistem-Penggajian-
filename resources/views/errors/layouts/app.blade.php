<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>@yield('title') - {{ config('app.name') }}</title>


  <link rel="shortcut icon" href="{{ asset('images/logo/laguna.png') }}" type="image/x-icon" />
  <link rel="stylesheet" href="{{ asset('css/main/app.css') }}">
  <link rel="stylesheet" href="{{ asset('css/error.css') }}">
  @vite([])
</head>

<body>
  <script src="{{ asset('js/initTheme.js') }}"></script>
  <div id="error">
    <div class="error-page container">
      <div class="col-md-8 col-12 offset-md-2">
        <div class="text-center">
          @yield('content')
        </div>
      </div>
    </div>
  </div>
</body>

</html>