#!/bin/bash

echo "Arrancando Apache..."
apache2-foreground &

echo "Limpiando y cargando configuración..."
php artisan config:clear
php artisan config:cache

echo "Esperando a la base de datos..."

for i in {1..30}; do
    if php artisan migrate:status > /dev/null 2>&1; then
        echo "Base de datos lista"

        php artisan migrate --force
        php artisan db:seed --force
        break
    fi

    echo "Intento $i: BD no disponible..."
    sleep 2
done

wait
