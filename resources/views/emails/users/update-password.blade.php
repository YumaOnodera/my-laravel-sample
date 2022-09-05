@component('mail::message')
# {{ __('mail.update_password.message', ['appName' => config('app.name'), 'user' => $user]) }}

{{ __('mail.update_password.line_01') }}

{{ __('mail.update_password.line_02', ['actionUrl' => $actionUrl]) }}

{{ __('mail.common.salutation', ['appName' => config('app.name')]) }}
@endcomponent
