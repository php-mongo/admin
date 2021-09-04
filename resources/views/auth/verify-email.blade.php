<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'PHP Mongo Admin') }}</title>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}" defer></script>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">

    <script type="text/javascript">
        window.Laravel = <?php echo json_encode([
            'csrfToken' => csrf_token(),
        ]); ?>
    </script>
</head>
<body>
<div id="app" class="container-fluid off-canvas-wrapper">
    <div id="app-layout">
        <div class="off-canvas-wrapper-inner">
            <header class="header-content">
                <nav class="navigation top-navigation grid-container" style="width: inherit">
                    <a  title="{{ __('global.logo') }}" href="/" class="router-link-logo">
                        <span class="logo"><img src="/img/logo-pma.png" /> </span>
                    </a>

                    <div class="u-pull-right">
                        <span class="country-flag ng-scope">
                        <img src="/img/flags/icons/Australia.ico" alt="{{ __('title.countryIsTitle') }} " title="{{ __('title.countryIsTitle') }}  Australia" style="">
                    </span>
                    </div>
                </nav>
            </header>
            <div class="content-container text-center">

                <div class="row justify-content-center grid-container">
                    <div class="column">
                        <div class="card">
                            <p>&nbsp;</p>
                            <H2>{{ __('auth.verifyEmailTitle') }}</H2>
                            <p>
                                {{ __('auth.verifyInfo') }}
                            </p>
                            <p>&nbsp;</p>
                        </div>
                    </div>
                </div>

                <div class="row justify-content-center grid-container">
                    <div class="grid-x small-up-2 medium-up-3 large-up-4 ">
                        <div class="card">
                            <div class="card-header">
                                <p>&nbsp;</p>
                                <h3>{{ __('auth.verifyTitle') }}</h3>
                            </div>
                            <div class="card-body">
                                <div class="grid-x small-up-1 medium-up-2 large-up-3">
                                    <div class="cell"></div>
                                    <div class="cell">
                                        <form method="POST" action="{{ route('setup') }}">
                                            @csrf

                                            <div class="form-group row mb-0">
                                                <div class="col-md-8 offset-md-4">
                                                    <button type="submit" class="button">
                                                        {{ __('auth.verifySave') }}
                                                    </button>
                                                </div>
                                                <div><p>&nbsp;</p></div>
                                            </div>
                                        </form>
                                    </div>
                                    <div class="cell"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @include('public.footer')
            </div>
        </div>
    </div>
</div>
</body>
</html>
