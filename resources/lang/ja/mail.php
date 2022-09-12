<?php

return [
    'common' => [
        'subcopy' => '「:actionText」ボタンをクリックできない場合は、次のURLをコピーしてWebブラウザに貼り付けます。',
        'salutation' => ':appNameサポート'
    ],
    'email_verification' => [
        'subject' => 'メールアドレスを認証して下さい',
        'line_01' => ':appNameアカウント :user が登録されました。',
        'line_02' => '次のリンクをクリックして、メールアドレスを確認してください。',
        'action' => 'メールアドレスの確認',
        'line_03' => 'このメールに心当たりがない場合は、無視していただいて問題ありません。'
    ],
    'reset_password' => [
        'subject' => 'パスワードの再設定',
        'line_01' => '次のリンクをクリックするとパスワードの再設定が行えます。',
        'action'  => 'パスワードの再設定',
        'line_02' => 'このリンクの有効期限は:count分です。',
        'line_03' => 'このメールに心当たりがない場合は、無視していただいて問題ありません。'
    ],
    'update_password' => [
        'subject' => 'パスワードが変更されました',
        'message' => ':appNameアカウント :user のパスワードが変更されました。',
        'line_01' => 'お客様がこの変更を行った場合は問題ありません。',
        'line_02' => 'このメールに心当たりがない場合は、[パスワードを再設定](:actionUrl)してアカウントを保護してください。'
    ],
    'destroy' => [
        'subject' => ':appNameアカウントの退会手続きを受け付けました。',
        'message' => ':appNameアカウント :user の退会手続きを受け付けました。',
        'line_01' => 'アカウント情報は退会手続きから:period日間のみ保持されます。
            :period日以降を過ぎますと、完全に削除されアカウントの復活はできませんのでご注意ください。',
        'line_02' => ':appNameをご利用いただきありがとうございます。'
    ]
];