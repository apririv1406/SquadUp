#!/bin/bash

echo "Arrancando Apache..."
apache2-foreground &

echo "Limpiando y cargando configuración..."
php artisan config:clear
php artisan config:cache

echo "Esperando a la base de datos..."

# Intentar migrar hasta que funcione
for i in {1..30}; do
    echo "Intento $i: ejecutando migraciones..."

    php artisan migrate --force && php artisan db:seed --force

    if [ $? -eq 0 ]; then
        echo "Migraciones ejecutadas correctamente"
        break
    fi

    echo "Migraciones fallaron, reintentando..."
    sleep 3
done

wait

