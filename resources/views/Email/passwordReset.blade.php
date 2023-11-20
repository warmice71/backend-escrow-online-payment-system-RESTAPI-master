@component('mail::message')
# Change Password Request

Click the button below to change your password

@component('mail::button', ['url' => $url])
Reset Password
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
