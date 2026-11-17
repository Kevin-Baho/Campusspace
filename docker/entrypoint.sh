#!/bin/sh
set -e
php artisan config:cache
php artisan route:cache
php artisan view:cache
# Migrasi non-destruktif (hanya membuat yang belum ada). Set RUN_MIGRATIONS=true bila ingin otomatis.
if [ "$RUN_MIGRATIONS" = "true" ]; then php artisan migrate --force; fi
exec "$@"
