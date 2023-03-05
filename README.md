# start-project-base-setting

## 環境構築

```
 docker run --rm \
 -u "$(id -u):$(id -g)" \
 -v $(pwd):/var/www/html \
 -w /var/www/html \
 laravelsail/php81-composer:latest \
 composer install --ignore-platform-reqs

```

env ファイルをもらう。

```
./vendor/bin/sail up
```

docker のコンテナの中に入る

```
docker-compose exec laravel.test bash
```

マイグレート！

```
php artisan migrate
```

## テストの実行コマンド

```
composer test
```
### ファイル指定する時
```php
composer test -- ./tests/Feature/SampleControllerTest.php
```

### ブランチ運用
- mainは本番環境
URI: `https://sample.com`
- developは開発環境
URI: `https://dev.sample.com`

## コーディングルール
https://github.com/alexeymezenin/laravel-best-practices/blob/master/japanese.md
