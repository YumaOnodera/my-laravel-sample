<?php

return [
    // 1ページに表示する件数
    'PER_PAGE' => [
        'PAGINATE' => 10, // 通常ページング
        'CURSOR_PAGINATE' => 15, // カーソルページング
    ],
    'USER_DESTROY_PERIOD' => 30, // 論理削除されたユーザーアカウントの物理削除までの期間
];
