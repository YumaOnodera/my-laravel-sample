# larastep

<p align="center">
<img src="https://img.shields.io/github/issues/YumaOnodera/larastep" alt="issues">
<img src="https://img.shields.io/github/forks/YumaOnodera/larastep" alt="issues">
<img src="https://img.shields.io/github/stars/YumaOnodera/larastep" alt="issues">
<img src="https://img.shields.io/github/license/YumaOnodera/larastep" alt="issues">
</p>

## larastepについて
larastepはアプリケーションのAPIを手軽に開発するためのスターターキットです。  
Laravel9をベースに認証・認可、ユーザー管理、投稿、検索といった基本的な機能を実現するためのAPIをサンプルとして初めから実装しています。
本ドキュメントの手順通りにいくつかの設定を済ませるだけで、面倒な設定を省いて簡単にアプリケーションの土台を作成できます。

## 設計思想
プログラム構成は以下サイトで紹介されている設計思想を参考にしています。
開発に入る前にぜひ一度目を通してみてください。
[5年間 Laravel を使って辿り着いた，全然頑張らない「なんちゃってクリーンアーキテクチャ」という落としどころ](https://zenn.dev/mpyw/articles/ce7d09eb6d8117 "設計思想")

## 実装済みの内容
- 環境変数のサンプル
- .gitignoreの設定
- Dockerコンテナの設定
- エラーハンドリングの設定
- ログ出力の設定
- 日本語ローカライズ
- 認証・認可の実装
- メール送信の設定
- 検索エンジンの設定
- ユーザーAPI、投稿API、コメントAPIのサンプル実装
- 各種APIドキュメント

アプリケーションの開発を進める上で必要な基本的な開発環境構築はすでに済ませてあります。  
必要に応じてプログラムをカスタマイズしてください。

## Dockerコンテナ
- php:8.1-fpm-alpine
- nginx:alpine
- mysql/mysql-server:8.0
- getmeili/meilisearch:latest
- mailhog/mailhog:latest
- swaggerapi/swagger-ui:latest
- swagger-merger

PHPとnginxはalpineベースの軽量コンテナです。特にPHPは最低限のモジュールしか搭載していないので、必要なモジュールがあれば適時追加してください。  
MySQLは8.0でバージョン固定にしてあります。必要に応じてバージョンを変えてください。  
meilisearchはシンプルで高速なオープンソースの検索エンジンです。  
mailhogはローカル環境でメールの受信をテストできるメールサーバです。  
swagger-uiはAPIドキュメントを表示するためのコンテナです。  
swagger-mergerを用いることで、分割して作成したSwaggerファイルをswagger-ui用に自動でマージできます。

## モデルの説明
以下は、larastepで用意したモデルです。  
あくまでサンプルとして用意しただけなので、アプリケーションの要件に合わせて自由にカスタマイズしてください。

### User
ユーザーのモデルです。  
削除は論理削除としています。  
「MassPrunable」によって、論理削除されたユーザーは30日経過で物理削除するよう実装しています。  
Laravel Scoutと連携し「Searchable」によってユーザー名による検索機能を実装しています。

### Post
投稿のモデルです。  
Laravel Scoutと連携し「Searchable」によって投稿文による検索機能を実装しています。

### Comment
投稿に紐づくコメントのモデルです。

## 主なディレクトリの説明
### app/Console/Kernel.php
バッチコマンドを定義します。

### app/Exceptions
エラーハンドリングを定義します。

### app/Http/Controllers
Requestから受けとった値をUseCaseに渡して処理を行い、Resourceを通じてフロント側にレスポンスを返却する役割を担います。  
Controllerに書く内容は極力最小限にし、具体的な処理はUseCaseで行います。

### app/Http/Requests
各APIごとの認可処理とバリデーションを担当します。

### app/Http/Resources
フロント側に返却するレスポンスの整形処理を行います。

### app/Mail
メールの送信処理を担当します。  
他に似た処理を担うクラスとしてNotificationsもありますが、シンプルなメール配信であればMailのほうが実装しやすいかと思います。

### app/Models
データ同士の関連性やデータの保存形式等を定義します。検索エンジンで使用するデータの設定なども行います。

### app/Providers
Laravelアプリケーション起動時に実行されるアプリケーション設定を定義します。  
larastepでは、app/Providers/AppServiceProvider.phpファイルにログ出力設定や検索エンジン設定を定義しています。  
また、app/Providers/AuthServiceProvider.phpファイルに管理者フラグによる認可ロジックを定義しています。  
共通の認可ロジックを新たに追加・修正したい場合は、app/Providers/AuthServiceProvider.phpファイルを編集します。

### app/Services
Servicesはlarastepで独自に追加したものです。  
複数のUseCasesで使用されることを想定した共通処理を定義します。  
また、ネットワークやファイルストレージへのアクセスへのアクセスもServicesに定義します。

### app/UseCases
UseCasesはlarastepで独自に追加したものです。  
データの取得や追加といった各API固有の処理はUseCasesにまとめます。  
コードの保守性を保つためにも、UseCasesで定義するpublicメソッドは`__invoke`のみに限定すべきです。  
UseCasesに記述する処理が複雑になった際に、`__invoke`で記述している処理の一部をprivateメソッドを用いて分割する分には問題ないでしょう。

### config
configでは、アプリケーションの設定ファイルを設置します。  
中でもconst.phpファイルはlarastepで独自に追加したファイルで、複数のクラスで使用される定数を定義します。

### docker 
dockerコンテナに関する設定ファイルを設置するディレクトリです。

### resources/lang
言語ファイルをまとめたディレクトリです。  
ja/mail.phpファイルはlarastepで独自に追加したファイルで、メール配信で使用する文章を定義しています。

### resources/views
主にメールや通知のフォーマットを定義します。

### storage/logs
ログファイルの保管場所です。  
larastepでは日ごとにログを集計しています。  
また、ログファイルの保存期間は7日間に設定しています。  
ログ出力の設定を変える場合はconfig/logging.phpファイルで設定できます。

### swagger
swaggerディレクトリはlarastepで独自に追加したもので、APIに関するドキュメントをまとめます。

APIの仕様はswagger/src/pathsに定義し、APIのレスポンス仕様はswagger/src/resourcesに切り出しています。  
また、モデルの仕様はswagger/src/schemasに定義します。  
swagger/src/index.jsonファイルではSwaggerのルーティングを担います。

swagger/openapi.jsonファイルは実際にSwaggerドキュメントの内容をUIに反映させるためのファイルです。  
swagger/src/index.phpファイルの内容が更新されたタイミングで、
Dockerコンテナ「swagger-merger」により自動的に更新されるので、手動では編集しなくて良いファイルです。

### tests
テストコードをまとめるディレクトリです。  
tests/TestCase.phpファイルには、各テスト共通の処理を記載します。  
larastepでは、各テストケースで更新されたデータが他のテストケースに影響を与えないよう、
各テストケースごとにデータを初期化する処理を加えています。

## 環境構築
### 前提
ローカル環境に以下がインストールされていること
- docker v20.10.17以上
- docker compose v2.10.2以上

### 手順
- リポジトリをクローン
```
git clone https://github.com/YumaOnodera/larastep.git アプリ名
```

以下、アプリケーション直下で実行

- 環境変数ファイルを作成
```
cp .env.example .env
cp .env.testing.example .env.testing
```

- envと.env.testingに「APP_NAME」「DB_DATABASE」「DB_PASSWORD」「MEILISEARCH_KEY」を設定

例）
```
APP_NAME=larastep
DB_DATABASE=larastep
DB_PASSWORD=password
MEILISEARCH_KEY=masterKey
```

env.testingの場合は、テスト環境用のデータベース名がアプリケーション用のデータベース名と被らないよう設定する。

例）
```
DB_DATABASE=larastep_testing
```

- docker/mysql/sql/create-testing-database.shを編集

CREATE DATABASE文とGRANT文を編集する。  
.envと.env.testingで設定したデータベース名（DB_DATABASE）と同じ名前をそれぞれ設定する。

- Dockerコンテナを作成して起動
```
docker compose up --build -d
```

- 起動中コンテナの一覧を表示し、PHPコンテナの「NAMES」を確認
```
docker ps
```

例）
> larastep-php-1

- 確認した「NAMES」でPHPコンテナにアクセス

例）
```
docker exec -it larastep-php-1 /bin/sh
```

- PHPコンテナ内でパッケージのインストール
```
composer install
```

- アプリケーションキーの発行
```
php artisan key:generate
php artisan key:generate --env=testing
```

- マイグレーション
```
php artisan migrate
php artisan migrate --env=testing
```

- シーディング
```
php artisan db:seed
```

- PHPコンテナから抜ける
```
exit
```

- sailのエイリアスを設定
```
// シェルファイルを開く
vi ~/.zshrc

// 下記を記述
alias sail='[ -f sail ] && sh sail || sh vendor/bin/sail'

// シェルファイルを閉じた後、下記コマンドで反映させる
source ~/.zshrc
```

- Laravelのバージョン確認し、同時にLaravel Sailが有効であることを確認
```
sail artisan -V
```

以降、composerやartisanコマンドは`sail`をつけることで、アプリケーション直下で実行できるようになります。

- テストが成功することを確認
```
sail artisan test
```

## URL
フロントエンド
```
http://localhost:3000
```

Swagger UI
```
http://localhost:8080
```

MailHog
```
http://localhost:8025
```

Meilisearch
```
http://localhost:7700
```

## よく使うコマンド集
### Dockerの操作
- Dockerコンテナを作成して起動
```
docker compose up --build -d
```

### マイグレーションとシーディング
マイグレーション
```
sail artisan migrate
```
シーディング
```
sail artisan db:seed
```

### 検索インデックス
検索インデックスにインポート

例）
```
sail artisan scout:import "App\Models\Post"
```
検索インデックスから全レコード削除

例）
```
sail artisan scout:flush "App\Models\Post"
```

### テスト
単体テスト
```
sail test ファイルパス
```
全体テスト
```
sail test
```
並列テスト
```
sail test --parallel
```
テストカバレッジ
```
sail test --coverage
```
並列テストとテストカバレッジ
```
sail test --parallel --coverage
```

### コードフォーマット
ルールチェックを実行しファイルの修正を行う
```
vendor/bin/pint
```
ルールチェックを実行しファイルの修正と修正箇所の表示を行う
```
vendor/bin/pint -v
```
ルールチェックのみを実行
```
vendor/bin/pint --test
```
※コードフォーマットはPHPコンテナ内で実行する必要があります。

### ファイル作成
モデル作成
```
sail artisan make:model ファイル名
```
マイグレーション作成
```
// テーブルの作成
sail artisan make:migration ファイル名 --create=テーブル名

// テーブルの編集
sail artisan make:migration ファイル名 --table=テーブル名
```
ファクトリ作成
```
sail artisan make:factory ファイル名
```
シーダー作成
```
sail artisan make:seeder ファイル名
```
コントローラ作成
```
sail artisan make:controller ファイル名
```
リソース作成
```
sail artisan make:resource ファイル名
```
フォームリクエスト作成
```
sail artisan make:request ファイル名
```
テスト作成
```
sail artisan make:test ファイル名
```

## ライセンス
[MIT license](https://opensource.org/licenses/MIT) © YumaOnodera
