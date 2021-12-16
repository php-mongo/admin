@component('mail::message')
    <h1>New Control User</h1>

    Hi {{ $user->name }},
              You recently set-up a new Control User account at {{ config('app.url') }} on {{ date("d-m-Y") }}.

    UserName: {{ $user->user }}
    Email: {{ $user->getEmail() }}

    Helpful Links:

    @component('mail::button', ['url' => config('app.url') . "/" ])
        Login Here
    @endcomponent

    @component('mail::button', ['url' => config('app.url') . "/setup"])
        Reset your password here
    @endcomponent

    <br>
    System Email,<br>
                {{ config('app.name') }}

@endcomponent
