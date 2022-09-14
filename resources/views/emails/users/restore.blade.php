@component('mail::message')
# {{ __('mail.restore.message', ['appName' => config('app.name'), 'user' => $user]) }}

{{ __('mail.restore.line_01') }}

{{ __('mail.common.salutation', ['appName' => config('app.name')]) }}
@endcomponent
