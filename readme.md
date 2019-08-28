# Laravel Worker

Laravel Worker for multi purposes or multi queue-servers.

## Requirements
 * PHP 7.1 above
   * ext-json
 * Laravel 5.5
 * redis, rabbitmq or more
 
## Installation
 
```bash
composer require yupmin/laravel-worker
```
 
Install config
 
```bash
php artisan vendor/publish --provider=Yupmin\Worker\ServiceProvider
```
 
## How to use
 
Running Worker.
 
```bash
php artisan worker:run echo
...
[2019-08-28T14:36:47+00:00] worker:run echo "worker running..."
```
 
Send Message to Worker

```bash
php artisan worker:send echo Test
```
 
## License
 
The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
