#!/bin/bash

# Esperar a que la base de datos esté lista
echo "Esperando a la base de datos..."
until php artisan migrate:status > /dev/null 2>&1; do
sleep 2
done

echo "Base de datos lista. Ejecutando migraciones y seeders..."
php artisan migrate:fresh --force
php artisan db:seed --force

echo "Arrancando Apache..."
apache2-foreground
