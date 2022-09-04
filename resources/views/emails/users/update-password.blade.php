@component('mail::message')
# @lang(config('app.name') . "アカウント $user のパスワードが変更されました。")<br>
お客様がこの変更を行っていない場合、次のリンクからパスワードの再設定を行ってください。

@component('mail::button', ['url' => $actionUrl, 'color' => 'primary'])
パスワードを再設定する
@endcomponent

@if (! empty($salutation))
{{ $salutation }}
@else
{{ config('app.name') }}
@endif

@slot('subcopy')
@lang("\"パスワードを再設定する\" ボタンをクリックできない場合は、次のURLをコピーしてWebブラウザに貼り付けます。: ")
<span class="break-all">[{{ $actionUrl }}]({{ $actionUrl }})</span>
@endslot
@endcomponent
