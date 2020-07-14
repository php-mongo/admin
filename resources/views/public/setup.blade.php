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

                    <div class="right">
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
                            <H2>{{ __('setup.title') }}</H2>
                            <p>
                                <strong>{{ __('setup.p1') }}</strong>
                            </p>
                            <p>
                                {{ __('setup.p2') }}
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
                                <h3>{{ __('setup.formTitle') }}</h3>
                            </div>
                            <div class="card-body">
                                <div class="grid-x small-up-1 medium-up-2 large-up-3">
                                    <div class="cell"></div>
                                    <div class="cell">
                                        <form method="POST" action="{{ route('setup') }}">
                                            @csrf

                                            <div class="form-group row">
                                                <label for="email" class="col-md-4 col-form-label text-md-right">{{ __('setup.name') }}</label>

                                                <div class="col-md-6">
                                                    <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" placeholder="{{__('setup.namePlaceholder')}}" required autocomplete="name" autofocus>

                                                    @error('name')
                                                    <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                                    @enderror
                                                </div>
                                            </div>

                                            <div class="form-group row">
                                                <label for="email" class="col-md-4 col-form-label text-md-right">{{ __('setup.username') }}</label>

                                                <div class="col-md-6">
                                                    <input id="user" type="text" class="form-control @error('user') is-invalid @enderror" name="user" value="{{ old('user') }}" placeholder="{{__('setup.usernamePlaceholder')}}" required autocomplete="user" autofocus>

                                                    @error('user')
                                                    <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                                    @enderror
                                                </div>
                                            </div>

                                            <div class="form-group row">
                                                <label for="password" class="col-md-4 col-form-label text-md-right">{{ __('setup.password') }}</label>

                                                <div class="col-md-6">
                                                    <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" placeholder="{{__('setup.passwordPlaceholder')}}" required autocomplete="current-password">

                                                    @error('password')
                                                    <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                                    @enderror
                                                </div>
                                            </div>

                                            <div class="form-group row">
                                                <label for="email" class="col-md-4 col-form-label text-md-right">{{ __('setup.email') }}</label>

                                                <div class="col-md-6">
                                                    <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" placeholder="{{__('setup.emailPlaceholder')}}" required autocomplete="email" autofocus>

                                                    @error('email')
                                                    <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                                    @enderror
                                                </div>
                                            </div>

                                            <div class="form-group row mb-0">
                                                <div class="col-md-8 offset-md-4">
                                                    <button type="submit" class="button">
                                                        {{ __('setup.save') }}
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
                <div class="row justify-content-left grid-container">
                    <div class="grid-x small-up-2 medium-up-3 large-up-4 ">
                        <div class="card">
                            <div class="card-body" style="padding-top: 12px;">
                                <p>@lang('global.copyright')</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
