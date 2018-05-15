1. 改 composer.json，添加:

```php
"require-dev": {
    "dingo/api": "2.0.0-alpha2",
    "tymon/jwt-auth": "1.0.0-rc.2",
    "basicit/lumen-vendor-publish": "^2.1"
},
```

2. composer install

3. cp .env.example .env, 并修改内容:

```php
CACHE_DRIVER=memcached
QUEUE_DRIVER=redis
```

4. 修改 bootstrap/app.php，注册服务:

```php
$app->routeMiddleware([
    'auth' => App\Http\Middleware\Authenticate::class,
]);

$app->register(Dingo\Api\Provider\LumenServiceProvider::class);
$app->register(App\Providers\AuthServiceProvider::class);
$app->register(Tymon\JWTAuth\Providers\LumenServiceProvider::class);

$app['Dingo\Api\Exception\Handler']->setErrorFormat([
    'error' => [
        'message' => ':message',
        'errors' => ':errors',
        'code' => ':code',
        'status_code' => ':status_code',
        'debug' => ':debug'
    ]
]);
```

5. 执行 php artisan jwt:secret

6. 在 routes/web.php 里添加:

```php
$app->withFacades();

$app->withEloquent();

$api = app('Dingo\Api\Routing\Router');

$api->version(['v1', 'v2'], function ($api) {
    $api->get('users/{id}', 'App\Http\Controllers\ExampleController@show');
});

$api->version('v1', function ($api) {
    $api->get('goods/{id}', 'App\Http\Controllers\V1\UserController@index');
});

$api->version('v2', function ($api) {
    $api->get('goods/{id}', 'App\Http\Controllers\V2\UserController@index');
});
```

7. 创建 config 文件夹。并将 /vendor/laravel/lumen-framework/config/auth.php 复制过去.并修改内容:

```php
'guards' => [
    'api' => [
        'driver' => 'jwt',
        'provider' => 'users'
    ],
],
'providers' => [
    'users' => [
        'driver' => 'eloquent',
        'model' => \App\User::class,
    ],
],
```

8. 要使用监听事件，要添加 redis 服务，把 app.php 里服务注册改为：

```
$app->register(App\Providers\AppServiceProvider::class);
$app->register(Dingo\Api\Provider\LumenServiceProvider::class);
$app->register(App\Providers\AuthServiceProvider::class);
$app->register(App\Providers\ArtisanServiceProvider::class);
$app->register(Tymon\JWTAuth\Providers\LumenServiceProvider::class);
$app->register(Illuminate\Redis\RedisServiceProvider::class);
$app->register(App\Providers\EventServiceProvider::class);
```
添加 Redis, Event,并在项目根目录启动监听```php artisan queue:work --queue=default```
