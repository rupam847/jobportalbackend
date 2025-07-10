@component('mail::message')
Hello {{ $user->name }},

Welcome to our app! 🎉

We're excited to have you on board.

@component('mail::button', ['url' => url('/')])
Get Started
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent