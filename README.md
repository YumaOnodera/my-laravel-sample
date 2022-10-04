# my-laravel-sample

<p align="center">
<img src="https://img.shields.io/github/issues/YumaOnodera/my-laravel-sample" alt="issues">
<img src="https://img.shields.io/github/forks/YumaOnodera/my-laravel-sample" alt="issues">
<img src="https://img.shields.io/github/stars/YumaOnodera/my-laravel-sample" alt="issues">
<img src="https://img.shields.io/github/license/YumaOnodera/my-laravel-sample" alt="issues">
</p>

## my-laravel-sampleについて
my-laravel-sampleはアプリケーションのAPIを手軽に開発するためのスターターキットです。  
Laravel9をベースに認証・認可、ユーザー管理、投稿、検索といった基本的な機能を実現するためのAPIをサンプルとして初めから実装しています。

本ドキュメントの手順通りにいくつかの設定を済ませるだけで、面倒な設定を省いて簡単にアプリケーションの土台を作成できます。

なお、プログラム構成は以下サイトで紹介されている設計思想を参考にしています。
実際に開発に入る前に一度目を通しておくことをおすすめします。  
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
以下は、my-laravel-sampleで用意したモデルです。  
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
他に似た処理を担うディレクトリとしてapp/Notificationsもありますが、シンプルなメール配信であればapp/Mailのほうが実装しやすいかと思います。

### app/Models
データ同士の関連性やデータの保存形式等を定義します。検索エンジンで使用するデータの設定なども行います。

### app/Notifications
さまざまな配信チャンネルに通知を送信します。  
Mailクラスと異なるのは、メールだけでなく、SMSやSlackでの配信も行える点です。

Laravelの認証スターターキットLaravel Breezeで認証機能を作成する際は、
メール確認とパスワードリセットのメール通知はNotificationsで搭載されます。  
my-laravel-sampleではLaravel Breezeで作成されたメール通知を日本語化するにあたり、Notificationsのメソッドをオーバーライドしています。  
しかし上記のような特別なケースを除けば、メール配信はMailクラスを用いる方が使い勝手が良いでしょう。  

my-laravel-sampleでは、メールはMailで、それ以外の通知はNotificationsで、という使い分けを想定しています。

### app/Providers
Laravelアプリケーション起動時に実行されるアプリケーション設定を定義します。  
my-laravel-sampleでは、app/Providers/AppServiceProvider.phpファイルにログ出力設定や検索エンジン設定を定義しています。  
また、app/Providers/AuthServiceProvider.phpファイルに管理者フラグによる認可ロジックを定義しています。  
共通の認可ロジックを新たに追加・修正したい場合は、app/Providers/AuthServiceProvider.phpファイルを編集します。

### app/Services
Servicesディレクトリはmy-laravel-sampleで独自に追加したディレクトリです。  
複数のUseCasesで使用されることを想定した共通処理を定義します。  
また、ネットワークやファイルストレージへのアクセスへのアクセスもServicesに定義します。

### app/UseCases
UseCasesディレクトリはmy-laravel-sampleで独自に追加したディレクトリです。  
データの取得や追加といった各API固有の処理はUseCasesにまとめます。  
コードの保守性を保つためにも、UseCasesで定義するpublicメソッドは`__invoke`のみに限定すべきです。  
UseCasesに記述する処理が複雑になった際に、`__invoke`で記述している処理の一部をprivateメソッドを用いて分割する分には問題ないでしょう。

### config
configでは、アプリケーションの設定ファイルを設置します。  
中でもconst.phpファイルはmy-laravel-sampleで独自に追加したファイルで、複数のクラスで使用される定数を定義します。

### docker 
dockerコンテナに関する設定ファイルを設置するディレクトリです。

### resources/lang
言語ファイルをまとめたディレクトリです。  
ja/mail.phpファイルはmy-laravel-sampleで独自に追加したファイルで、メール配信で使用する文章を定義しています。

### resources/views
主にメールや通知のフォーマットを定義します。

### storage/logs
ログファイルの保管場所です。  
my-laravel-sampleでは日ごとにログを集計しています。  
また、ログファイルの保存期間は7日間に設定しています。  
ログ出力の設定を変える場合はconfig/logging.phpファイルで設定できます。

### swagger
swaggerディレクトリはmy-laravel-sampleで独自に追加したディレクトリで、APIに関するドキュメントをまとめます。

APIの仕様はswagger/src/pathsディレクトリに定義し、APIのレスポンス仕様はswagger/src/resourcesディレクトリに切り出しています。  
また、モデルの仕様はswagger/src/schemasディレクトリに定義します。  
swagger/src/index.jsonファイルではSwaggerのルーティングを担います。

swagger/openapi.jsonファイルは実際にSwaggerドキュメントの内容をUIに反映させるためのファイルです。  
swagger/src/index.phpファイルの内容が更新されたタイミングで、
Dockerコンテナ「swagger-merger」により自動的に更新されるので、手動では編集しなくて良いファイルです。

### tests
テストコードをまとめるディレクトリです。  
tests/TestCase.phpファイルには、各テスト共通の処理を記載します。  
my-laravel-sampleでは、各テストケースで更新されたデータが他のテストケースに影響を与えないよう、
各テストケースごとにデータを初期化する処理を加えています。

## 環境構築
### 前提
ローカル環境に以下がインストールされていること
- docker v20.10.17以上
- docker-compose v2.10.2以上

### 手順
- リポジトリをクローン
```
git clone https://github.com/YumaOnodera/my-laravel-sample.git アプリ名
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
APP_NAME=my-laravel-sample
DB_DATABASE=my_laravel_sample
DB_PASSWORD=password
MEILISEARCH_KEY=masterKey
```

env.testingの場合は、テスト環境用のデータベース名がアプリケーション用のデータベース名と被らないよう設定する。

例）
```
DB_DATABASE=my_laravel_sample_testing
```

- docker/mysql/sql/create-testing-database.shを編集

CREATE DATABASE文とGRANT文を編集する。  
.envと.env.testingで設定したデータベース名（DB_DATABASE）と同じ名前をそれぞれ設定する。

- Dockerコンテナを作成して起動
```
docker-compose up --build -d
```

- 起動中コンテナの一覧を表示し、PHPコンテナの「NAMES」を確認
```
docker ps
```

例）
> my-laravel-sample-php-1

- 確認した「NAMES」でPHPコンテナにアクセス

例）
```
docker exec -it my-laravel-sample-php-1 /bin/sh
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

- Laravelのバージョン確認し、同時にLaravel Sailが有効であることを確認
```
./vendor/bin/sail artisan -V
```

以降、composerやartisanコマンドは`./vendor/bin/sail`をつけることで、アプリケーション直下で実行できるようになります。

- テストが成功することを確認
```
./vendor/bin/sail php artisan test
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
### マイグレーションとシーディング
マイグレーション
```
./vendor/bin/sail php artisan migrate
```
シーディング
```
./vendor/bin/sail php artisan db:seed
```

### テスト
単体テスト
```
./vendor/bin/sail php artisan test ファイルパス
```
全体テスト
```
./vendor/bin/sail php artisan test
```
並列テスト
```
./vendor/bin/sail php artisan test --parallel
```
テストカバレッジ
```
./vendor/bin/sail php artisan test --coverage
```
並列テストとテストカバレッジ
```
./vendor/bin/sail php artisan test --parallel --coverage
```

### コードフォーマット
ルールチェックを実行しファイルの修正も行う
```
vendor/bin/pint
```
ルールチェックのみを実行
```
vendor/bin/pint --test
```
※コードフォーマットはPHPコンテナ内で実行する必要があります。

### ファイル作成
モデル作成
```
./vendor/bin/sail php artisan make:model ファイル名
```
マイグレーション作成
```
./vendor/bin/sail php artisan make:migration ファイル名 --table=モデル
```
ファクトリ作成
```
./vendor/bin/sail php artisan make:factory ファイル名
```
シーダー作成
```
./vendor/bin/sail php artisan make:seeder ファイル名
```
コントローラ作成
```
./vendor/bin/sail php artisan make:controller ファイル名
```
リソース作成
```
./vendor/bin/sail php artisan make:resource ファイル名
```
フォームリクエスト作成
```
./vendor/bin/sail php artisan make:request ファイル名
```
テスト作成
```
./vendor/bin/sail php artisan make:test ファイル名
```

## 今後の開発方針
my-laravel-sampleはもともとは私自身がLaravelを学び直す上で作ったものです。
引き続き開発を続けていく予定ですが、まったり自分のペースで進めていくので更新ペースはまばらになるかと思います。

今のところ予定している開発内容は次の通りです。
- インフラの設定追加
- CircleCIの設定追加
- リポジトリ名の変更

今後も機能追加・修正等予定しているので、READMEもその都度更新していきます。

機能を盛り込みすぎると扱いにくくなる恐れもあるので、なるべくミニマルな設計で開発していく方針です。

## ライセンス
[MIT license](https://opensource.org/licenses/MIT) © YumaOnodera
