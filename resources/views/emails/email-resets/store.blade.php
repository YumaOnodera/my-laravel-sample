@component('mail::message')
{{ __('mail.reset_email.line_01') }}

@component('mail::button', ['url' => $actionUrl, 'color' => 'primary'])
{{ __('mail.reset_email.action')}}
@endcomponent

{{ __('mail.reset_email.line_02', ['count' => $expiration]) }}

{{ __('mail.reset_email.line_03') }}

{{ __('mail.common.salutation', ['appName' => config('app.name')]) }}
@endcomponent
