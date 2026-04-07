#!/bin/bash

echo "Arrancando Apache primero..."
apache2-foreground &

echo "Esperando a la base de datos..."

# Reintentos limitados (MUY IMPORTANTE)
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

# Mantener contenedor vivo
wait
