@component('mail::message')
    <h1>New Admin User</h1>

    Hi {{ $user->name }}
            A new 'admin' account has been created for at {{ config('app.url') }} on {{ date("d-m-Y") }}.
            Your password will be provided to you securely using an alternative comms method.

    UserName: {{ $user->username }}
    Email: {{ $user->getEmail() }}

    Helpful Links:

    @component('mail::button', ['url' => "{{ config('app.url') }}/"])
        Login Here
    @endcomponent

    Application Email,<br>
    {{ config('app.name') }}

@endcomponent
