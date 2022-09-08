@component('mail::message')
# {{ __('mail.destroy.message', ['appName' => config('app.name'), 'user' => $user]) }}

{{ __('mail.destroy.line_01', ['period' => config('const.USER_DESTROY_PERIOD')]) }}

{{ __('mail.destroy.line_02', ['appName' => config('app.name')]) }}

{{ __('mail.common.salutation', ['appName' => config('app.name')]) }}
@endcomponent
