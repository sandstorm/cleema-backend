#!/bin/bash
set -ex

# start nginx in background
caddy start --config /etc/caddy/Caddyfile

./artisan storage:link

# start PHP-FPM
exec /usr/local/sbin/php-fpm

# start cronjob
echo | nohup php artisan schedule:work > ./storage/logs/cronjob.logs &
